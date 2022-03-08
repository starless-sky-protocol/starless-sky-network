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

class sign_controller
{
    public static function view()
    {
        $id = @$GLOBALS["request"]->id;
        $private_key = @$GLOBALS["request"]->private_key;

        if ($private_key == null) {
            add_message("error", "Private key not provided");
            return json_response();
        }

        $res = \svc\signing\view($private_key, $id);

        return json_response($res);
    }

    public static function add()
    {
        $private_key = @$GLOBALS["request"]->private_key;
        $public_key = @$GLOBALS["request"]->public_key;
        $message = @$GLOBALS["request"]->message;
        $expires = @$GLOBALS["request"]->expires;

        if ($private_key == null) {
            add_message("error", "Private key not provided");
            return json_response();
        }
        if ($public_key == null) {
            add_message("error", "Target public key/address not provided");
            return json_response();
        }
        if ($expires == null) {
            add_message("error", "Contract expiration time not provided");
            return json_response();
        }

        $res = \svc\signing\add($private_key, $public_key, $message, $expires);

        return json_response($res);
    }

    public static function browse()
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

        $res = \svc\signing\browse($private_key, $folder, $pagination_data);

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

    public static function opt()
    {
        $id = @$GLOBALS["request"]->id;
        $private_key = @$GLOBALS["request"]->private_key;
        $term = @$GLOBALS["request"]->term;

        if ($private_key == null) {
            add_message("error", "Private key not provided");
            return json_response();
        }
        if ($term == null) {
            add_message("error", "Term not provided");
            return json_response();
        }

        $res = \svc\signing\sign($private_key, $term, $id);

        return json_response($res);
    }
}
