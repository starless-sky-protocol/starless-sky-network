<?php

function generante_random_private_key()
{
    $length = PRIVATE_KEY_GEN_LENGTH;
    $r = openssl_random_pseudo_bytes($length, $force);
    return SLS_PRIVATE_KEY_PREFIX . base64_encode($r);
}
