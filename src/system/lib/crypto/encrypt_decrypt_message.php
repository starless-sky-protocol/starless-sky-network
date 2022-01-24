<?php

define("IV_LENGTH", 16);

function get_random_iv() {
    $b2 = new BLAKE3();
    return base64_encode($b2->hash($_ENV["BASE_SYMETRIC_16BYTES_IV"] . time() . rand(), IV_LENGTH - 4));
}

function encrypt_message($message, $key)
{
    if (strlen($_ENV["BASE_SYMETRIC_16BYTES_IV"]) != IV_LENGTH) {
        add_message("fatal", "BASE_SYMETRIC_16BYTES_IV must have a fixed size of 16 chars.");
    }

    $iv = get_random_iv();

    $output = openssl_encrypt(
        $message,
        'AES-128-CBC',
        hmac_blake3($key, $_ENV["BASE_SYMETRIC_KEY"]),
        0,
        $iv
    );

    return $iv . $output;
}

function decrypt_message($message, $key)
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
        hmac_blake3($key, $_ENV["BASE_SYMETRIC_KEY"]),
        0,
        $iv
    );

    return $output;
}
