<?php

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
