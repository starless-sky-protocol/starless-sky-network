<?php

namespace svc\identity;

use BIP39;

trait mnemonic_parse
{
    public function mnemonic_parse()
    {
        $mnemonic = $GLOBALS["request"]->mnemonic;

        $private_key_h = BIP39::mnemonicToEntropy($mnemonic);
        $private_key = SLOPT_BIN_TO_PRIVATE_KEY["prefix"] . $private_key_h;
        $public_key = algo_gen_hash($private_key, SLOPT_PRIVATE_KEY_TO_PUBLIC_KEY);

        if($private_key_h == false) {
            return json_response();
        } else {
            add_message("info", "Key pair restored from mnemonic");
        }

        return json_response([
            "private_key" => $private_key,
            "public_key" => $public_key,
            "host" => config("dns"),
        ]);
    }
}
