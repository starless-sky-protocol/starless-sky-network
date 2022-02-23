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

class messages_controller
{
    public function edit($id)
    {
        $private_key = @$GLOBALS["request"]->private_key;
        $message = @$GLOBALS["request"]->message;

        if ($private_key == null) {
            add_message("error", "Private key not provided");
            return json_response();
        }

        $res = \svc\message\edit($private_key, $id, $message);

        return json_response($res);
    }

    public function browse()
    {
        $private_key = @$GLOBALS["request"]->private_key;
        $folder = @$GLOBALS["request"]->folder;
        $pagination_data = @$GLOBALS["request"]->pagination_data;

        if ($private_key == null) {
            add_message("error", "Private key not provided");
            return json_response();
        }
        if ($folder == null) {
            add_message("error", "Folder not provided");
            return json_response();
        }
        if ($pagination_data == null) {
            add_message("error", "Pagination data not provided");
            return json_response();
        }

        $res = \svc\message\browse($private_key, $folder, $pagination_data);

        return json_response(
            [
                "pagination_data" => [
                    "total" => 0,
                    "query" => 0
                ],
                "messages" => $res
            ]
        );
    }

    public function add()
    {
        $private_key = @$GLOBALS["request"]->private_key;
        $public_keys = @$GLOBALS["request"]->public_keys;
        $message = @$GLOBALS["request"]->message;

        if ($private_key == null) {
            add_message("error", "Private key not provided");
            return json_response();
        }
        if ($public_keys == null || !is_array($public_keys)) {
            add_message("error", "Public keys not provided");
            return json_response();
        }

        $res = \svc\message\add($private_key, $public_keys, $message);

        return json_response($res);
    }

    public function read($id)
    {
        $private_key = @$GLOBALS["request"]->private_key;

        if ($private_key == null) {
            add_message("error", "Private key not provided");
            return json_response();
        }

        $res = \svc\message\read($private_key, $id);

        return json_response($res);
    }

    public function delete($id)
    {
        $private_key = @$GLOBALS["request"]->private_key;

        if ($private_key == null) {
            add_message("error", "Private key not provided");
            return json_response();
        }

        $res = \svc\message\delete($private_key, $id);

        return json_response($res);
    }
}
