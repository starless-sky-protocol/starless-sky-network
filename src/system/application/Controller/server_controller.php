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

namespace Controller;

class server_controller
{
    function ping()
    {
        return json_response(\svc\server\ping());
    }

    function get_closed_blocks()
    {
        add_message("info", "Fetching closed blocks on this network");
        $b = array_map("basename", glob(TRANSACTIONS_PATH . '*.{closed}', GLOB_BRACE));
        return json_response($b);
    }

    function read_block($block)
    {
        if(is_file(TRANSACTIONS_PATH . $block)) {
            add_message("info", "Block fetched");
            return json_response(json_decode(file_get_contents(TRANSACTIONS_PATH . $block)));
        } else {
            add_message("error", "Block not found");
            return json_response();
        }
    }
}
