<?php

namespace Controller;

use Inphinit\Http\Request;

class PrivateKeyController
{
    public function GetRandomPrivateKey()
    {
        $private_key = generante_random_private_key();
        $public_key = private_key_to_public_key($private_key, 0);

        add_message("info", "Keypair successfully generated");

        return json_response([
            "private_key" => $private_key,
            "public_key" => $public_key
        ]);
    }
}
