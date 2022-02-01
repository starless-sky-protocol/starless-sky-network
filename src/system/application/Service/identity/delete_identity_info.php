<?php

namespace svc\identity;

trait delete_identity_info
{
    public function delete_identity_info()
    {
        $private_key = $GLOBALS["request"]->private_key;
        $public_key = algo_gen_hash($private_key, SLOPT_PRIVATE_KEY_TO_PUBLIC_KEY);
        $public_key_h = algo_gen_hash($public_key, SLOPT_PUBLIC_KEY_DIRNAME);

        if (!is_private_key_valid($private_key)) {
            add_message("error", "Invalid private key.");
            return json_response();
        }

        if (!is_file($filepath = IDENTITY_PATH . $public_key_h)) {
            add_message("error", "There is no identity associated with this public key on this network.");
            return json_response();
        }

        unlink($filepath);

        add_message("info", "Network Identity info deleted");

        return json_response();
    }
}
