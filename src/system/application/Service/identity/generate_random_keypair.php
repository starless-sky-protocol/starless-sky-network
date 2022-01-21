<?php

namespace svc\identity;

trait generate_random_keypair
{
    public function generate_random_keypair()
    {
        $private_key = generante_random_private_key();
        $public_key = private_key_to_public_key($private_key);

        add_message("info", "Keypair successfully generated");

        return json_response([
            "private_key" => $private_key,
            "public_key" => $public_key
        ]);
    }
}
