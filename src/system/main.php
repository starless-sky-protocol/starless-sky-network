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

use Inphinit\Routing\Route;

Route::set('POST', '/{:[a-zA-Z0-9.-]+:}{:/?:}', "transport_controller:transport");

/*
Route::set('GET', '/ping', "server_controller:ping");
Route::set('GET', '/bc/list', "server_controller:get_closed_blocks");
Route::set('GET', '/bc/read/{:[a-zA-Z0-9.-]+:}{:/?:}', "server_controller:read_block");

Route::set('GET', '/identity/generate-keypair', "identity_controller:generate_random_keypair_handler");
Route::set('VIEW', '/identity/auth', "identity_controller:auth_handler");
Route::set('PUT', '/identity{:/?:}', "identity_controller:set_identity_info_handler");
Route::set('VIEW', '/identity{:/?:}', "identity_controller:get_identity_info_handler");
Route::set('DELETE', '/identity{:/?:}', "identity_controller:delete_identity_info_handler");

Route::set('LIST', '/messages{:/?:}', "messages_controller:browse");
Route::set('VIEW', '/messages/{:[a-zA-Z0-9]+:}{:/?:}', "messages_controller:read");
Route::set('PUT', '/messages/{:[a-zA-Z0-9]+:}{:/?:}', "messages_controller:edit");
Route::set('DELETE', '/messages/{:[a-zA-Z0-9]+:}{:/?:}', "messages_controller:delete");
Route::set('POST', '/messages{:/?:}', "messages_controller:add");

Route::set('VIEW', '/sign/{:[a-zA-Z0-9]+:}{:/?:}', "sign_controller:view");
Route::set('LIST', '/sign{:/?:}', "sign_controller:browse");
Route::set('POST', '/sign{:/?:}', "sign_controller:add");
Route::set('PUT', '/sign/{:[a-zA-Z0-9]+:}{:/?:}', "sign_controller:sign");
*/