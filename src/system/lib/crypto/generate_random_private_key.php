<?php

function generante_random_private_key()
{
    $iv = $_ENV["BASE_PRIVATE_KEY_IV"];
    $iv_len = strlen($iv);

    if ($iv_len == 0) {
        add_message("fatal", "Cannot generate private key: network private key IV is empty");
        json_response([], true);
        die();
    }

    $length = PRIVATE_KEY_GEN_LENGTH - get_algo_length("");
    $r = openssl_random_pseudo_bytes($length, $force);
    $m = algo_gen_base34_hash($iv) . $r;
    $d = encrypt_message($m, "");
    return SLS_PRIVATE_KEY_PREFIX . $d;
}
