<?php

function is_hash_valid($public_key)
{
    if (strpos($public_key, "0x") != 0) {
        return false;
    }
    if (!preg_match('/^[a-f0-9x]+$/', $public_key)) {
        return false;
    }
    if ((strlen($public_key) - 2) * 2 != SLOPT_PUBLIC_KEY_ADDRESS["length"] * 4) {
        return false;
    }
    return true;
}
