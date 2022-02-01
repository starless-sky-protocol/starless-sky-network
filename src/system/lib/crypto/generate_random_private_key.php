<?php

DEFINE("PRIVATE_KEY_GEN_LENGTH", 256);

function generante_random_private_key()
{
    $server_id = config("crypto.private_key_server_id");
    $id_len = strlen($server_id);
    if ($id_len == 0) {
        add_message("fatal", "Cannot generate private key: network private key ID is empty");
        json_response([], true);
        die();
    }

    $length = PRIVATE_KEY_GEN_LENGTH - get_algo_length();
    $r = openssl_random_pseudo_bytes($length, $force);
    $m = algo_gen_hash($server_id, SLOPT_DEFAULT) . $r;
    $d = encrypt_message($m, "");
    return $d;
}
