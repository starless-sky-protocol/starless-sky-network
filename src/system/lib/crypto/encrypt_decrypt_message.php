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

define("IV_LENGTH", 16);

function get_random_iv()
{
    $b2 = new BLAKE3();
    return base64_encode($b2->hash(config("crypto_key") . time() . rand(), IV_LENGTH - 4));
}

function encrypt_message($message, $sharedKey)
{
    $iv = get_random_iv();

    $output = openssl_encrypt(
        $message,
        'AES-128-CBC',
        hmac_blake3($sharedKey, config("crypto_key")),
        0,
        $iv
    );

    return $iv . $output;
}

function decrypt_message($message, $sharedKey)
{
    $iv_length = strlen(get_random_iv());

    if (strlen($message) <= $iv_length) {
        add_message("fatal", "Invalid message. It should be bigger than " . $iv_length . " chars.");
    }

    $iv = substr($message, 0, $iv_length);
    $data = substr($message, $iv_length, 99999999);
    $output = openssl_decrypt(
        $data,
        'AES-128-CBC',
        hmac_blake3($sharedKey, config("crypto_key")),
        0,
        $iv
    );

    return $output;
}
