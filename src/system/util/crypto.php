<?php

DEFINE("SLS_ID_PREFIX", "0xSID");
DEFINE("SLS_HASH_PREFIX", "0xSH");
DEFINE("SLS_PUBLIC_KEY_PREFIX", "0xSPB");
DEFINE("SLS_PRIVATE_KEY_PREFIX", "0xSPV");
DEFINE("SKYID_INSTANCE", $_ENV["SKYID_INSTANCE"]);
DEFINE("PRIVATE_KEY_GEN_LENGTH", 256);

function secure_strcmp($h0, $h1)
{
    $h0_len = strlen($h0);
    $h1_len = strlen($h1);

    if ($h0_len != $h1_len) {
        return false;
    }

    $h0_chars = str_split($h0);
    $h1_chars = str_split($h1);

    $r = true;
    for ($i = 0; $i < strlen($h0); $i++) {
        $r &= $h0_chars[$i] == $h1_chars[$i];
    }

    return $r;
}

function gen_skyid()
{
    return SLS_ID_PREFIX . uniqid() . SKYID_INSTANCE . base_convert(rand(100000, 999999), 10, 32);
}

function get_algo_length($prefix)
{
    return strlen($prefix) + BLAKE3_XOF_LENGTH * 2;
}

function algo_gen_base34_hash($content)
{
    $h = array_values(unpack("C*", hmac_blake3($content, $_ENV["BASE_HMAC_KEY"], true)));
    $b = "";
    for ($i = 0; $i < count($h); $i++) {
        $b .= str_pad(base_convert($h[$i], 10, 34), 2, "z");
    }
    return $b;
}

function is_private_key_valid($private_key)
{
    if (strpos($private_key, SLS_PRIVATE_KEY_PREFIX) != 0) {
        return false;
    }
    $base64 = str_replace(SLS_PRIVATE_KEY_PREFIX, "", $private_key);
    if (strlen(base64_decode($base64)) != PRIVATE_KEY_GEN_LENGTH) {
        return false;
    }
    return true;
}

function is_public_key_valid($public_key)
{
    if (strpos($public_key, SLS_PUBLIC_KEY_PREFIX) != 0) {
        return false;
    }
    if (!preg_match('/^[a-x0-9z]+$/', str_replace(SLS_PUBLIC_KEY_PREFIX, "", $public_key))) {
        return false;
    }
    if (strlen($public_key) != get_algo_length(SLS_PUBLIC_KEY_PREFIX)) {
        return false;
    }
    return true;
}

function generante_random_private_key()
{
    $length = PRIVATE_KEY_GEN_LENGTH;
    $r = openssl_random_pseudo_bytes($length, $force);
    return SLS_PRIVATE_KEY_PREFIX . base64_encode($r);
}

function encrypt_message($message, $private_key_hash)
{
    if(strlen($_ENV["BASE_SYMETRIC_16BYTES_IV"]) != 16) {
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
    if(strlen($_ENV["BASE_SYMETRIC_16BYTES_IV"]) != 16) {
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

function private_key_to_public_key($private_key_content)
{
    $output = algo_gen_base34_hash($private_key_content);
    return SLS_PUBLIC_KEY_PREFIX . $output;
}
