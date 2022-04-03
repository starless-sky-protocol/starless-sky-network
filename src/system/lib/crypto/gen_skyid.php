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

function generate_random_string($length = 10)
{
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyz', ceil($length / strlen($x)))), 1, $length);
}

function gen_skyid()
{
    return uniqid() . generate_random_string(16);
}

function transaction_id($zero = false)
{
    if ($zero == false) {
        $m = (int)(round(hrtime(true) * 1000));
    } else {
        $m = "0";
    }
    return str_pad($m . rand(100000, 999999), 28, "0", STR_PAD_LEFT);
}
