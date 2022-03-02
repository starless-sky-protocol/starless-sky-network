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

function auth(string $raw_private_key): string|bool
{
    $private_key = load_from_private($raw_private_key);

    if ($private_key == false) {
        add_message("error", "Invalid or not authenticated private key received");
        return false;
    }

    $sender_public_key_obj = $private_key->getPublicKey();
    $sender_public_key_raw = $sender_public_key_obj->toString("PKCS8");
    $sender_public_key_hash = algo_gen_hash($sender_public_key_raw, SLOPT_PUBLIC_KEY_ADDRESS);

    add_message("info", "Private key is authenticated in this network");
    return $sender_public_key_hash;
}
