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

class transport_controller
{
    function transport($command)
    {
        switch (strtolower($command)) {
            case "message.send":
                return messages_controller::add();
            case "message.list":
                return messages_controller::browse();
            case "message.read":
                return messages_controller::read();
            case "message.edit":
                return messages_controller::edit();
            case "message.delete":
                return messages_controller::delete();

            case "identity.gen-private-key":
                return identity_controller::generate_random_keypair_handler();
            case "identity.set-public-info":
                return identity_controller::set_identity_info_handler();
            case "identity.get-public-info":
                return identity_controller::get_identity_info_handler();
            case "identity.delete-public-info":
                return identity_controller::delete_identity_info_handler();
            case "identity.auth":
                return identity_controller::auth_handler();

            case "contract.send":
                return sign_controller::add();
            case "contract.list":
                return sign_controller::browse();
            case "contract.read":
                return sign_controller::view();
            case "contract.opt":
                return sign_controller::opt();

            case "server.ping":
                return server_controller::ping();
            case "server.bc.read":
                return server_controller::read_block();
            case "server.bc.list":
                return server_controller::get_closed_blocks();

            default:
                add_message("error", "Unknown command received");
                return json_response();
        }
    }
}
