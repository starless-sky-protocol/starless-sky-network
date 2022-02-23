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

function add(string $from_private_key, string $to_public_key, string $message, int $expires)
{
    $private_key_raw = $from_private_key;
    $private_key = load($private_key_raw);
    if ($private_key == false) {
        add_message("error", "Invalid private key received");
        return false;
    }

    $from_public_key_obj = $private_key->getPublicKey();
    $from_public_key_hash = algo_gen_hash($from_public_key_obj->toString("PKCS8"), SLOPT_PUBLIC_KEY_ADDRESS);
    $from_public_key_h = algo_gen_hash($from_public_key_hash, SLOPT_PUBLIC_KEY_DIRNAME);

    $to_public_key_h = algo_gen_hash($to_public_key, SLOPT_PUBLIC_KEY_DIRNAME);

    if (!is_hash_valid($to_public_key)) {
        add_message("error", "Invalid receiver public key.");
        return false;
    }
    if (strlen($message) > parse_hsize($size = config("information.sign_message_max_size"))) {
        add_message("error", "Sign message is greater than the server limit: " + $size);
        return false;
    }

    if (!is_dir($from_dir = CONTRACT_FROM_PATH . $from_public_key_h)) {
        mkdir($from_dir, 775);
    }
    if (!is_dir($to_dir = CONTRACT_TO_PATH . $to_public_key_h)) {
        mkdir($to_dir, 775);
    }

    $id = gen_skyid();
    $now = time();
    $sign_data = [
        "id" => $id,
        "issued" => $now,
        "expires" => min($expires, config("information.sign_max_expiration")),
        "message" => $message,
        "issuer" => [
            "public_key" => $from_public_key_hash
        ],
        "signer" => [
            "public_key" => $to_public_key
        ],
        "status" => [
            "sign_status" => null,
            "date" => null
        ]
    ];
    
    $puk = load_from_public_hash($to_public_key);
    if ($puk == false) {
        add_message("warn", "Cannot send contract to unauthenticated public keys.");
        return false;
    }

    $k = shared_key($private_key, $puk);
    $id_h = algo_gen_hash($id, SLOPT_SKYID_HASH);
    {
        $sign_data_enc = $sign_data;
        $sign_data_enc["id"] = encrypt_message($sign_data["id"], $k);
        $sign_data_enc["message"] = encrypt_message($sign_data["message"], $k);
        $sign_data_enc["status"] = encrypt_message(json_encode($sign_data["status"]), $k);

        $sign_json = json_encode($sign_data_enc);
        file_put_contents($from_dir . "/" . $id_h, encrypt_message($sign_json, ""));
        file_put_contents($to_dir . "/" . $id_h, encrypt_message($sign_json, ""));
    }

    add_message("info", "Contract request created successfully");
    return $sign_data;
}
