<?php

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
