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

namespace svc\signing;

function browse(string $private_key, string $folder, array|object $pagination_data)
{
    $private_key_raw = $private_key;
    $private_key = load_from_private($private_key_raw);
    if ($private_key == false) {
        add_message("error", "Invalid or not authenticated private key received");
        return false;
    }

    $public_key_obj = $private_key->getPublicKey();
    $public_key_hash = algo_gen_hash($public_key_obj->toString("PKCS8"), SLOPT_PUBLIC_KEY_ADDRESS);
    $public_key_h = algo_gen_hash($public_key_hash, SLOPT_PUBLIC_KEY_DIRNAME);

    switch (strtolower($folder)) {
        case "inbox":
            $dir = CONTRACT_TO_PATH;
            break;
        case "sent":
            $dir = CONTRACT_FROM_PATH;
            break;
        default:
            add_message("error", "Invalid folder. It must be 'inbox' or 'sent'.");
            return false;
    }

    tryagain:
    if (!is_dir($directory = $dir . $public_key_h)) {
        add_message("warn", "Your query did not return any information.");
        return [
            "pagination_data" => [
                "total" => 0,
                "query" => 0
            ],
            "contracts" => []
        ];
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
        $data_content = file_get_contents($message);
        $contract_decrypted = json_decode(decrypt_message($data_content, ""));

        $sharedKey = strcmp($contract_decrypted->issuer->public_key, $public_key_hash) == 0
            ? shared_key($private_key, load_from_public_hash($contract_decrypted->signer->public_key))
            : shared_key($private_key, load_from_public_hash($contract_decrypted->issuer->public_key));
        
        $data[] = [
            "id" => decrypt_message($contract_decrypted->id, $sharedKey),
            "issued" => $contract_decrypted->issued,
            "from" => $contract_decrypted->issuer->public_key,
            "to" => $contract_decrypted->signer->public_key,
            "message" => decrypt_message($contract_decrypted->message, $sharedKey),
            "title" => decrypt_message($contract_decrypted->title, $sharedKey),
            "sign_status" => json_decode(decrypt_message($contract_decrypted->status, $sharedKey))->sign_status
        ];
    }

    usort($data, function ($a, $b) {
        return $a["issued"] <=> $b["issued"];
    });

    add_message("info", "Query performed successfully");

    return [
        "pagination_data" => [
            "total" => count($glob),
            "query" => count($data)
        ],
        "contracts" => array_reverse($data)
    ];
}
