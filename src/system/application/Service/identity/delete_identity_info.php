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

function delete_identity_info(string $raw_private_key): bool
{
    $private_key = load_from_private($raw_private_key);
    if ($private_key == false) {
        add_message("error", "Invalid or not authenticated private key received");
        return false;
    }

    $public_key_obj = $private_key->getPublicKey();
    $public_key_raw = $public_key_obj->toString("PKCS8");
    $public_key_hash = algo_gen_hash($public_key_raw, SLOPT_PUBLIC_KEY_ADDRESS);
    $public_key_h = algo_gen_hash($public_key_hash, SLOPT_PUBLIC_KEY_DIRNAME);
    $public_key_secret = algo_gen_hash($public_key_hash, SLOPT_PUBLIC_KEY_SECRET);

    if (!is_file($fname = IDENTITY_PATH . $public_key_h)) {
        add_message("error", "There is no identity associated with this private key on this network.");
        return false;
    }

    // load identity data
    $fdata = json_decode(decrypt_message(file_get_contents($fname), $public_key_secret), true);

    $fdata["public"]["name"] = null;
    $fdata["public"]["biography"] = null;
    $fdata["private"]["updated_at"] = time();

    $jsonData = json_encode($fdata);
    $encrypted = encrypt_message($jsonData, $public_key_secret);

    create_transaction("identity.delete-public-info", $public_key_hash, "", "", $jsonData);

    file_put_contents($fname, $encrypted);
    add_message("info", "Network Identity info deleted");

    return true;
}
