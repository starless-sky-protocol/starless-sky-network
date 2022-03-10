<?php

/*
    Project Starless Sky Protocol
    Copyright 2022 Project Principium and Starless Sky authors

    This project is distributed under the MIT License, that is, you can modify,
    publish or sell this file, as long as you have brief mention of the project
    and the code snippet used (if not all).

    The project is distributed under no warranty, that is, there is no
    responsibility for consequences or contents circulating in the
    networks created by this project.

    Editing this file will directly interfere with the functioning of your
    network. Unless you know what you're doing, read the documentation.
    If you think this edit is interesting for the project, submit a commit in the
    project official repository:

    https://github.com/starless-sky-protocol/starless-sky-network
*/

namespace svc\message;

function edit(string $from_private_key, string $id, array|object $message)
{
    if (config("information.allow_message_edit") == false) {
        add_message("error", "This SLS network doens't allow editing of messages.");
        return false;
    }

    $private_key = load_from_private($from_private_key);
    if ($private_key == false) {
        add_message("error", "Invalid or not authenticated private key received");
        return false;
    }

    $public_key = $private_key->getPublicKey();
    $public_key_h = algo_gen_hash($public_key->toString("PKCS8"), SLOPT_PUBLIC_KEY_ADDRESS);
    $public_key_d = algo_gen_hash($public_key_h, SLOPT_PUBLIC_KEY_DIRNAME);
    $id_h = algo_gen_hash($id, SLOPT_SKYID_HASH);

    if (!is_dir(SENT_PATH . $public_key_d)) {
        add_message("error", "Message not found");
        return false;
    }

    if (!is_file($file_path = SENT_PATH . $public_key_d . "/" . algo_gen_hash($id, SLOPT_SKYID_HASH))) {
        add_message("error", "Message not found");
        return false;
    }

    $message_content = file_get_contents($file_path);
    $message_decrypted = json_decode(decrypt_message($message_content, ""));

    if (!secure_strcmp($message_decrypted->pair->from, $public_key_h)) {
        add_message("error", "The private key does not match with the sender's private key of the message.");
        return false;
    }

    if (trim($message->content) == "" || trim($message->subject) == "") {
        add_message("error", "Message contents cannot be empty or whitespace.");
        return false;
    }

    $manifest_decoded = json_decode(decrypt_message($message_decrypted->manifest, $private_key->toString("PKCS8")));

    $now = time();
    $message_x = [
        "id" => $id,
        "content" => $message->content,
        "subject" => $message->subject,
        "read" => false,
        "manifest" => [
            "created_at" => $manifest_decoded->created_at,
            "updated_at" => $now,
            "is_modified" => true,
            "message_digest" => blake3($message->content . $message->subject)
        ],
        "pair" => [
            "from" => $message_decrypted->pair->from,
            "to" => $message_decrypted->pair->to
        ]
    ];

    if (!is_dir($b_path = SENT_PATH . $public_key_d)) {
        mkdir($b_path, 777);
    }

    $sharedKey = $private_key->toString("PKCS8");
    $message_y = $message_x;
    $message_y["id"] = encrypt_message($message_x["id"], $sharedKey);
    $message_y["manifest"] = encrypt_message(json_encode($message_x["manifest"]), $sharedKey);
    $message_y["content"] = encrypt_message($message->content, $sharedKey);
    $message_y["subject"] = encrypt_message($message->subject, $sharedKey);

    $message_json_data_for_sender = encrypt_message(json_encode($message_y), "");
    file_put_contents($b_path . "/" . $id_h, $message_json_data_for_sender);

    $sent = 0;
    foreach ($message_decrypted->pair->to as $public_key) {
        $public_key_h = algo_gen_hash($public_key, SLOPT_PUBLIC_KEY_DIRNAME);

        if (strcmp($public_key, $public_key_h) == 0) {
            add_message("warn", "Sender public key cannot be the same as the target public key. Ignoring this receiver.");
            continue;
        }

        if (!is_dir($b_path = INBOX_PATH . $public_key_h)) {
            mkdir($b_path, 777);
        }

        $puk = load_from_public_hash($public_key);
        if ($puk == false) {
            add_message("warn", "Cannot send message to unauthenticated public keys. Ignoring this receiver.");
            continue;
        }

        $sharedKey = shared_key($private_key, $puk);

        $message_y = $message_x;

        $message_y["id"] = encrypt_message($message_x["id"], $sharedKey);
        $message_y["manifest"] = encrypt_message(json_encode($message_x["manifest"]), $sharedKey);
        $message_y["content"] = encrypt_message($message->content, $sharedKey);
        $message_y["subject"] = encrypt_message($message->subject, $sharedKey);

        $message_y_json = json_encode($message_y);
        $message_json_data_for_receiver = encrypt_message($message_y_json, "");
        file_put_contents($b_path . "/" . $id_h, $message_json_data_for_receiver);

        $sent++;
    }

    $public_key_h = algo_gen_hash($private_key->getPublicKey()->toString("PKCS8"), SLOPT_PUBLIC_KEY_ADDRESS);

    create_transaction(
        "message.edit",
        $public_key_h,
        $message_decrypted->pair->to,
        $id,
        $message_x["id"] . $message_x["subject"] . $message_x["content"] . json_encode($message_x["manifest"]) . $public_key_h . json_encode($message_decrypted->pair->to)
    );

    add_message("info", "Message edited using sender's private key");
    return [
        "pair" => $message_decrypted->pair,
        "message_length" => hsize(strlen($message->content . $message->subject)),
        "id" => $id,
        "message_digest" => $message_x["manifest"]["message_digest"]
    ];
}
