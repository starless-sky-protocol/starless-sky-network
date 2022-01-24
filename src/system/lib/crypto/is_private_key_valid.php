<?php

function is_private_key_valid($private_key)
{
    $decrypted_data = decrypt_message($private_key, "");
    if (strlen($decrypted_data) != PRIVATE_KEY_GEN_LENGTH) {
        return false;
    }
    return true;
}
