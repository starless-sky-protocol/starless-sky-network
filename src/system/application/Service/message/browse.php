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

function browse(string $private_key, string $folder, array|object $pagination_data)
{
    $folder = $GLOBALS["request"]->folder;
    $private_key_raw = $GLOBALS["request"]->private_key;
    $private_key = load_from_private($private_key_raw);
    if ($private_key == false) {
        add_message("error", "Invalid or not authenticated private key received");
        return false;
    }
    $public_key = $private_key->getPublicKey();
    $public_key_h = algo_gen_hash($public_key->toString("PKCS8"), SLOPT_PUBLIC_KEY_ADDRESS);
    $public_key_d = algo_gen_hash($public_key_h, SLOPT_PUBLIC_KEY_DIRNAME);

    switch (strtolower($folder)) {
        case "inbox":
            $dir = INBOX_PATH;
            break;
        case "sent":
            $dir = SENT_PATH;
            break;
        default:
            add_message("error", "Invalid folder. It must be 'inbox' or 'sent'.");
            return false;
    }

    tryagain:
    if (!is_dir($directory = $dir . $public_key_d)) {
        add_message("warn", "Your query did not return any information.");
        return [];
    }

    $pagination_data = $GLOBALS["request"]->pagination_data;
    $glob = glob($directory . "/*");
    $data = [];

    if (count($glob) < $pagination_data->skip) {
        add_message("warn", "Pagination skip is greater than total messages on this private key");
    }
    if ($pagination_data->take == 0) {
        add_message("warn", "Pagination take is zero");
    }
    if ($pagination_data->take == -1) {
        add_message("warn", "Pagination take is infinite. All stored data is being returned.");
    }

    foreach (array_slice($glob, $pagination_data->skip, $pagination_data->take == -1 ? count($glob) : $pagination_data->take) as $message) {
        $message_content = file_get_contents($message);
        $message_decrypted = json_decode(decrypt_message($message_content, ""));

        $sharedKey = strcmp($message_decrypted->pair->from, $public_key_h) == 0
            ? $private_key->toString("PKCS8")
            : shared_key($private_key, load_from_public_hash($message_decrypted->pair->from));

        $manifest = json_decode(decrypt_message($message_decrypted->manifest, $sharedKey));

        $data[] = [
            "id" => decrypt_message($message_decrypted->id, $sharedKey),
            "created_at" => $manifest->created_at,
            "is_modified" => $manifest->is_modified,
            "from" => $message_decrypted->pair->from,
            "to" => $message_decrypted->pair->to,
            "read" => $message_decrypted->read,
            "message" => [
                "subject" => substr(decrypt_message($message_decrypted->subject, $sharedKey), 0, 32),
                "content" => substr(decrypt_message($message_decrypted->content, $sharedKey), 0, 32),
            ]
        ];
    }

    usort($data, function ($a, $b) {
        return $a["created_at"] <=> $b["created_at"];
    });

    add_message("info", "Query performed successfully");

    return array_reverse($data);
}
