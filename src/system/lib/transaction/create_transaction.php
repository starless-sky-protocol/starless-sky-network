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

function flush_transactions_block(bool $forced)
{
    $blocks = glob(TRANSACTIONS_PATH . '*.{closed}', GLOB_BRACE);
    $transactions = glob(TRANSACTIONS_PATH . '*.{open}', GLOB_BRACE);
    if (count($transactions) >= config("information.block_size") || $forced) {
        $block = [];

        foreach($transactions as $t) {
            $J = json_decode(file_get_contents($t), true);
            $block["transactions"][] = $J;
            unlink($t);
        }

        // get last block
        if(count($blocks) == 0) {
            // genesis block
            $block["header"] = blake3(0);
            $block["close_time"] = time();
        } else {
            // get last block
            $last_block = file_get_contents($blocks[count($blocks) - 1]);
            $block["header"] = blake3($last_block);
        }

        $id = transaction_id();
        $data = json_encode($block);
        file_put_contents(TRANSACTIONS_PATH . "b-" . $id . ".closed", $data);
    }
}

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
