<?php

define("SLOPT_DEFAULT",                 ["salt" => 0xf74b15, "iterations" => 1, "length" => 32, "prefix" => ""]);
define("SLOPT_PUBLIC_KEY_ADDRESS",      ["salt" => 0xff81db, "iterations" => 8, "length" => 64, "prefix" => "0x"]);
define("SLOPT_PUBLIC_KEY_DIRNAME",      ["salt" => 0xdf70cb, "iterations" => 2, "length" => 32, "prefix" => ""]);
define("SLOPT_PUBLIC_KEY_SECRET",       ["salt" => 0xb63ec0, "iterations" => 5, "length" => 32, "prefix" => ""]);
define("SLOPT_SKYID_HASH",              ["salt" => 0x7b92bf, "iterations" => 2, "length" => 16, "prefix" => "sky-"]);

function algo_gen_hash($content, $options)
{
    $s = $options["salt"];
    $r = $content;
    for ($i = 0; $i < $options["iterations"]; $i++) {
        $r = bin2hex(hmac_blake3($r, config("crypto_key") . $s, $options["length"]));
    }
    return $options["prefix"] . $r;
}
