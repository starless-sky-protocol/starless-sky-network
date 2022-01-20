<?php

function encrypt_message($message, $private_key_hash)
{
    if (strlen($_ENV["BASE_SYMETRIC_16BYTES_IV"]) != 16) {
        add_message("fatal", "BASE_SYMETRIC_16BYTES_IV must have a fixed size of 16 chars.");
    }

    $output = openssl_encrypt(
        $message,
        'AES-128-CBC',
        $_ENV["BASE_SYMETRIC_KEY"] . $private_key_hash,
        0,
        $_ENV["BASE_SYMETRIC_16BYTES_IV"]
    );

    return $output;
}

function decrypt_message($message, $private_key_hash)
{
    if (strlen($_ENV["BASE_SYMETRIC_16BYTES_IV"]) != 16) {
        add_message("fatal", "BASE_SYMETRIC_16BYTES_IV must have a fixed size of 16 chars.");
    }

    $output = openssl_decrypt(
        $message,
        'AES-128-CBC',
        $_ENV["BASE_SYMETRIC_KEY"] . $private_key_hash,
        0,
        $_ENV["BASE_SYMETRIC_16BYTES_IV"]
    );

    return $output;
}
