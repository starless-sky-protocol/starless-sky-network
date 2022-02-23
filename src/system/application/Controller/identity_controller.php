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

class identity_controller
{
    public function generate_random_keypair_handler()
    {
        return json_response(\svc\identity\generate_random_keypair());
    }

    public function set_identity_info_handler()
    {
        $private_key = @$GLOBALS["request"]->private_key;
        $public_info = @$GLOBALS["request"]->public;

        if ($private_key == null) {
            add_message("error", "Private key not provided");
            return json_response();
        }
        if ($public_info == null) {
            add_message("error", "Public info not provided");
            return json_response();
        }

        $res = \svc\identity\set_identity_info($private_key, $public_info, null);
        if ($res) {
            add_message("info", "Network Identity info modified");
        } else {
            add_message("error", "Cannot modify network identity info");
        }
        return json_response(["public_info" => $public_info, "public_address" => $res]);
    }

    public function delete_identity_info_handler() {
        $private_key = @$GLOBALS["request"]->private_key;

        if ($private_key == null) {
            add_message("error", "Private key not provided");
            return json_response();
        }

        \svc\identity\delete_identity_info($private_key);

        return json_response();
    }

    public function get_identity_info_handler()
    {
        $public_keys = @$GLOBALS["request"]->public_keys;

        if ($public_keys == null || !is_array($public_keys)) {
            add_message("error", "Public keys not provided");
            return json_response();
        }

        $res = \svc\identity\get_identity_info($public_keys);
        if ($res) {
            add_message("info", "Public keys fetched");
        } else {
            add_message("error", "Error while fetching messages");
        }
        return json_response($res);
    }
}
