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

function add(string $from_private_key, array $to_public_keys, array|object $message)
{
    $private_key_raw = $from_private_key;
    $private_key = load_from_private($private_key_raw);
    if ($private_key == false) {
        add_message("error", "Invalid or not authenticated private key received");
        return false;
    }

    $sender_public_key_obj = $private_key->getPublicKey();
    $sender_public_key_hash = algo_gen_hash($sender_public_key_obj->toString("PKCS8"), SLOPT_PUBLIC_KEY_ADDRESS);
    $sender_public_key_h = algo_gen_hash($sender_public_key_hash, SLOPT_PUBLIC_KEY_DIRNAME);

    if (!is_array($to_public_keys)) {
        add_message("error", "'public_keys' must be an array of public keys.");
        return false;
    } else {
        if (count($to_public_keys) == 0) {
            add_message("error", "There are no recipients in your submission.");
            return false;
        }
        if (count($to_public_keys) > $max = config("information.multicast_max_receivers")) {
            add_message("error", "Attempting to send messages to more recipients than the network allows ($max).");
            return false;
        }
        if (trim($message->content) == "" || trim($message->subject) == "") {
            add_message("error", "Message contents cannot be empty or whitespace.");
            return false;
        }
        if (strlen($message->content . $message->subject) >= parse_hsize($size = config("information.message_max_size"))) {
            add_message("error", "Message content cannot be bigger than " . $size . " bytes.");
            return false;
        }

        $now = time();
        $id = gen_skyid();
        $id_h = algo_gen_hash($id, SLOPT_SKYID_HASH);
        $message_x = [
            "id" => $id,
            "content" => $message->content,
            "subject" => $message->subject,
            "read" => false,
            "manifest" => [
                "created_at" => $now,
                "updated_at" => $now,
                "is_modified" => false,
                "message_digest" => blake3($message->content . $message->subject)
            ],
            "pair" => [
                "from" => $sender_public_key_hash,
                "to" => $to_public_keys
            ]
        ];

        if (!is_dir($b_path = SENT_PATH . $sender_public_key_h)) {
            mkdir($b_path, 775);
        }

        $sent = [];
        foreach ($to_public_keys as $public_key) {
            $public_key_h = algo_gen_hash($public_key, SLOPT_PUBLIC_KEY_DIRNAME);

            if (strcmp($public_key, $sender_public_key_hash) == 0) {
                add_message("warn", "Sender public key cannot be the same as the target public key. Ignoring this receiver.");
                continue;
            }

            if (!is_hash_valid($public_key)) {
                add_message("warn", "Received invalid hash. Ignoring this receiver.");
                continue;
            }

            if (!is_dir($b_path = INBOX_PATH . $public_key_h)) {
                mkdir($b_path, 775);
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

            $sent[] = $public_key;
        }
    }

    if (count($sent) == 0) {
        add_message("error", "The message was not sent to any recipients");
        return false;
    } else {
        $b_path = SENT_PATH . $sender_public_key_h;

        $sharedKey = $private_key->toString("PKCS8");
        $message_y = $message_x;
        $message_y["id"] = encrypt_message($message_x["id"], $sharedKey);
        $message_y["manifest"] = encrypt_message(json_encode($message_x["manifest"]), $sharedKey);
        $message_y["content"] = encrypt_message($message->content, $sharedKey);
        $message_y["subject"] = encrypt_message($message->subject, $sharedKey);

        $message_json_data_for_sender = encrypt_message(json_encode($message_y), "");
        file_put_contents($b_path . "/" . $id_h, $message_json_data_for_sender);
        add_message("info", "Message sent to " . count($sent) . " public keys");

        create_transaction(
            "message.send",
            $sender_public_key_hash,
            $sent,
            $id,
            $message_x["id"] . $message_x["subject"] . $message_x["content"] . json_encode($message_x["manifest"]) . $sender_public_key_hash . json_encode($sent)
        );

        return [
            "pair" => [
                "from" => $sender_public_key_hash,
                "to" => $sent
            ],
            "message_length" => hsize(strlen($message->content . $message->subject)),
            "id" => $id,
            "message_digest" => blake3($message->content . $message->subject)
        ];
    }
}
