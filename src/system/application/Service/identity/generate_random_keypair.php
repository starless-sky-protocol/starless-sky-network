<?php

namespace svc\identity;

use BIP39;

trait generate_random_keypair
{
    public function generate_random_keypair()
    {
        $private_key = algo_gen_hash(generante_random_private_key(), SLOPT_BIN_TO_PRIVATE_KEY);
        $public_key = algo_gen_hash($private_key, SLOPT_PRIVATE_KEY_TO_PUBLIC_KEY);

        add_message("info", "Unique keypair successfully generated for this network");

        return json_response([
            "private_key" => $private_key,
            "public_key" => $public_key,
            "mnemonic" => BIP39::entropyToMnemonic(substr($private_key, strlen(SLOPT_BIN_TO_PRIVATE_KEY["prefix"]))),
            "host" => config("dns"),
        ]);
    }
}
