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

function is_hash_valid($public_key)
{
    if (strpos($public_key, "0x") != 0) {
        return false;
    }
    if (!preg_match('/^[a-f0-9x]+$/', $public_key)) {
        return false;
    }
    if ((strlen($public_key) - 2) * 2 != SLOPT_PUBLIC_KEY_ADDRESS["length"] * 4) {
        return false;
    }
    return true;
}
