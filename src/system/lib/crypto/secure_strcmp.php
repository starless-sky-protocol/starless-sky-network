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

function secure_strcmp($h0, $h1)
{
    $h0_len = strlen($h0);
    $h1_len = strlen($h1);

    if ($h0_len != $h1_len) {
        return false;
    }

    $h0_chars = str_split($h0);
    $h1_chars = str_split($h1);

    $r = true;
    for ($i = 0; $i < strlen($h0); $i++) {
        $r &= $h0_chars[$i] == $h1_chars[$i];
    }

    return $r;
}
