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