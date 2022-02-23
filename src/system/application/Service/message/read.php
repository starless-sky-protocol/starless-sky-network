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

function read(string $private_key, string $id)
{
    $private_key = load($private_key);
    if ($private_key == false) {
        add_message("error", "Invalid private key received");
        return false;
    }
    
    $public_key = $private_key->getPublicKey();
    $public_key_h = algo_gen_hash($public_key->toString("PKCS8"), SLOPT_PUBLIC_KEY_ADDRESS);
    $public_key_d = algo_gen_hash($public_key_h, SLOPT_PUBLIC_KEY_DIRNAME);

    $dir = SENT_PATH;
    tryagain:
    if (!is_dir($dir . $public_key_d)) {
        if ($dir == SENT_PATH) {
            $dir = INBOX_PATH;
            goto tryagain;
        }
    }

    $file_path = $dir . $public_key_d . "/" . algo_gen_hash($id, SLOPT_SKYID_HASH);
    if (!is_file($file_path)) {
        if ($dir == SENT_PATH) {
            $dir = INBOX_PATH;
            goto tryagain;
        } else {
            add_message("error", "Message not found");
            return false;
        }
    }

    $message_content = file_get_contents($file_path);
    $message_decrypted = json_decode(decrypt_message($message_content, ""));

    $sharedKey = strcmp($message_decrypted->pair->from, $public_key_h) == 0
        ? $private_key->toString("PKCS8")
        : shared_key($private_key, load_from_public_hash($message_decrypted->pair->from));

    $m_content = decrypt_message($message_decrypted->content, $sharedKey);
    $m_subject = decrypt_message($message_decrypted->subject, $sharedKey);

    $message_decrypted->read = true;
    $data = [
        "id" => decrypt_message($message_decrypted->id, $sharedKey),
        "manifest" => json_decode(decrypt_message($message_decrypted->manifest, $sharedKey)),
        "pair" => $message_decrypted->pair,
        "size" => hsize(strlen($m_content . $m_subject)),
        "message" => [
            "subject" => $m_subject,
            "content" => $m_content
        ]
    ];

    $data_n = $message_decrypted;
    $data_n->read = true;
    file_put_contents($file_path, encrypt_message(json_encode($data_n), ""));

    return $data;
}
