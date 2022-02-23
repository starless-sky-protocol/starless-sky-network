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

use phpseclib3\Crypt\DH;

function load(string $rawKey)
{
    try {
        return DH::load($rawKey);
    } catch (\Throwable) {
        return false;
    }
}

function load_from_public_hash(string $public_key_hash)
{
    $public_key_dirname = algo_gen_hash($public_key_hash, SLOPT_PUBLIC_KEY_DIRNAME);
    $public_key_secret = algo_gen_hash($public_key_hash, SLOPT_PUBLIC_KEY_SECRET);

    if (!is_file(IDENTITY_PATH . $public_key_dirname)) {
        return false;
    }

    $json = json_decode(decrypt_message(file_get_contents(IDENTITY_PATH . $public_key_dirname), $public_key_secret), false);
    if(($json->private->public_key ?? null) == null) {
        return false;
    }

    return DH::load($json->private->public_key);
}
