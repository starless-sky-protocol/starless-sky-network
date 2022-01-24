<?php

function is_public_key_valid($public_key)
{
    if (strpos($public_key, "0x") != 0) {
        return false;
    }
    if (!preg_match('/^[a-f0-9x]+$/', $public_key)) {
        return false;
    }
    if (strlen($public_key) - 2 != get_algo_length()) {
        return false;
    }
    return true;
}
