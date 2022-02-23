<?php

namespace svc\identity;

trait generate_random_keypair
{
    public function generate_random_keypair()
    {
        $private_key = generante_random_private_key();
        $public_key = $private_key->getPublicKey();

        $public_key_pkcs8 = $public_key->toString("PKCS8");
        $public_key_derive = algo_gen_hash($public_key_pkcs8, SLOPT_PUBLIC_KEY_ADDRESS);

        file_put_contents(PUBLIC_KEY_DERIVES_PATH . $public_key_derive, encrypt_message($public_key_pkcs8, SLOPT_PUBLIC_KEY_SECRET["salt"]));

        add_message("info", "Unique keypair successfully generated for this network");
        return json_response([
            "private_key" => $private_key->toString("PKCS8"),
            "public_address" => $public_key_derive,
            "host" => config("dns"),
        ]);
    }
}
