<?php

namespace svc\identity;

trait get_identity_info
{
    public function get_identity_info()
    {
        $public_key = $GLOBALS["request"]->public_key;
        $public_key_h = algo_gen_base34_hash($public_key);

        if (!is_public_key_valid($public_key)) {
            add_message("error", "Invalid public key.");
            return json_response();
        }

        if(!is_file(IDENTITY_PATH . $public_key_h)) {
            add_message("error", "There is no identity associated with this public key on this network.");
            return json_response();
        }

        $encryptedData = file_get_contents(IDENTITY_PATH . $public_key_h);
        $raw = decrypt_message($encryptedData, $public_key);

        $identity_data = json_decode($raw);

        add_message("info", "Network Identity info fetched");

        return json_response($identity_data->public);
    }
}
