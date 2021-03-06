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

function create_transaction(string $command, ?string $from_public_address = null, array|string $to_public_keys = null, ?string $sky_id = null, ?string $content = null)
{
    $id = transaction_id();
    $content = [
        "command" => $command,
        "from" => $from_public_address,
        "to" => $to_public_keys,
        "sky_id" => $sky_id,
        "content" => blake3($content ?? ""),
        "time" => time(),
        "id" => $id
    ];
    $j = json_encode($content);

    $GLOBALS["transaction"] = $content;
    file_put_contents(TRANSACTIONS_PATH . "t-" . $id . ".open", $j);
    flush_transactions_block(false);
    return $id;
}
