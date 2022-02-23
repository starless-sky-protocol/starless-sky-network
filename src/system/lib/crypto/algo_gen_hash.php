<?php

/*
    Project Starless Sky Protocol
    Copyright 2022 Project Principium and Starless Sky authors

    This project is distributed under the MIT License, that is, you can modify,
    publish or sell this file, as long as you have brief mention of the project
    and the code snippet used (if not all).

    The project is distributed under no warranty, that is, there is no
    responsibility for consequences or contents circulating in the
    networks created by this project.

    Editing this file will directly interfere with the functioning of your
    network. Unless you know what you're doing, read the documentation.
    If you think this edit is interesting for the project, submit a commit in the
    project official repository:

    https://github.com/starless-sky-protocol/starless-sky-network
*/

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
