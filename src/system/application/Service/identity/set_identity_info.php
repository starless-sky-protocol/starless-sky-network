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

namespace svc\identity;

function set_identity_info(string $raw_private_key, mixed $public_info, mixed $private_info): string|bool
{
    $private_key_raw = $raw_private_key;
    $private_key = $private_info == null ? load_from_private($private_key_raw) : load($private_key_raw);

    if ($private_key == false) {
        add_message("error", "Invalid or not authenticated private key received");
        return false;
    }

    $sender_public_key_obj = $private_key->getPublicKey();
    $sender_public_key_raw = $sender_public_key_obj->toString("PKCS8");
    $sender_public_key_hash = algo_gen_hash($sender_public_key_raw, SLOPT_PUBLIC_KEY_ADDRESS);
    $sender_public_key_h = algo_gen_hash($sender_public_key_hash, SLOPT_PUBLIC_KEY_DIRNAME);
    $sender_public_key_secret = algo_gen_hash($sender_public_key_hash, SLOPT_PUBLIC_KEY_SECRET);

    if (strlen(json_encode($public_info)) >= $max_size = parse_hsize(config("information.message_max_size"))) {
        add_message("error", "Information content cannot be bigger than $max_size bytes.");
        return false;
    }

    // load identity data
    $fname = IDENTITY_PATH . $sender_public_key_h;
    $fdata = [];
    if (is_file($fname)) {
        $fdata = json_decode(decrypt_message(file_get_contents($fname), $sender_public_key_secret), true);
    } else {
        if ($private_info == null) {
            add_message("error", "Cannot authenticate the provided private key");
            return false;
        }
    }

    if ($public_info) {
        if (is_array($public_info)) $public_info = (object)$public_info;
        $fdata["public"]["name"] = $public_info->name ?? null;
        $fdata["public"]["biography"] = $public_info->biography ?? null;
    }
    if ($private_info) {
        if (is_array($private_info)) $private_info = (object)$private_info;
        $fdata["private"]["public_key"] = $sender_public_key_raw;
        $fdata["private"]["public_key_address"] = $sender_public_key_hash;
        $fdata["private"]["attributes"] = $private_info->attributes ?? [];
    }
    $fdata["private"]["updated_at"] = time();

    $jsonData = json_encode($fdata);
    $encrypted = encrypt_message($jsonData, $sender_public_key_secret);

    create_transaction("identity.set-public-info", $sender_public_key_hash, "", "", $public_info?->name ?? "" . $public_info?->biography ?? "");

    file_put_contents($fname, $encrypted);
    return $sender_public_key_hash;
}
