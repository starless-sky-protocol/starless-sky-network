<?php

define("SLOPT_DEFAULT", ["salt" => 0xf74b15, "iterations" => 1, "length" => 32, "prefix" => ""]);
define("SLOPT_PRIVATE_KEY_TO_PUBLIC_KEY", ["salt" => 0x8ce47f, "iterations" => 3, "length" => 32, "prefix" => "0x"]);
define("SLOPT_PUBLIC_KEY_DIRNAME", ["salt" => 0xdf70cb, "iterations" => 2, "length" => 32, "prefix" => ""]);
define("SLOPT_PUBLIC_KEY_KEY", ["salt" => 0xb63ec0, "iterations" => 5, "length" => 32, "prefix" => ""]);
define("SLOPT_SKYID_HASH", ["salt" => 0x7b92bf, "iterations" => 2, "length" => 16, "prefix" => "Sx"]);

function algo_gen_hash($content, $options)
{
    $s = blake3($options["salt"] . config("crypto.base_hash_salt"));
    $r = $content;
    for ($i = 0; $i < $options["iterations"]; $i++) {
        $r = bin2hex(hmac_blake3($r . $s, config("crypto.base_hmac_key") . $s, $options["length"]));
    }
    return $options["prefix"] . $r;
}
