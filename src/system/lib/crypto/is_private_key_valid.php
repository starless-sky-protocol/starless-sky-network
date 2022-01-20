<?php

function is_private_key_valid($private_key)
{
    if (strpos($private_key, SLS_PRIVATE_KEY_PREFIX) != 0) {
        return false;
    }
    $encoded_data = str_replace(SLS_PRIVATE_KEY_PREFIX, "", $private_key);
    $decrypted_data = decrypt_message($encoded_data, "");
    if (strlen($decrypted_data) != PRIVATE_KEY_GEN_LENGTH) {
        return false;
    }
    if (!secure_strcmp(substr($decrypted_data, 0, get_algo_length("")), algo_gen_base34_hash($_ENV["BASE_PRIVATE_KEY_IV"]))) {
        return false;
    }
    return true;
}
