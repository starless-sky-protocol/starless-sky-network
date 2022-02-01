<?php

use Inphinit\Routing\Route;

Route::set('GET', '/ping', "server_controller:ping");

Route::set('GET', '/identity/generate-keypair', "identity_controller:generate_random_keypair");
Route::set('POST', '/identity{:/?:}', "identity_controller:set_identity_info");
Route::set('GET', '/identity{:/?:}', "identity_controller:get_identity_info");
Route::set('DELETE', '/identity{:/?:}', "identity_controller:delete_identity_info");

Route::set('GET', '/messages{:/?:}', "messages_controller:browse");
Route::set('GET', '/messages/{:[a-zA-Z0-9]+:}{:/?:}', "messages_controller:read");
Route::set('PUT', '/messages/{:[a-zA-Z0-9]+:}{:/?:}', "messages_controller:edit");
Route::set('DELETE', '/messages/{:[a-zA-Z0-9]+:}{:/?:}', "messages_controller:delete");
Route::set('POST', '/messages{:/?:}', "messages_controller:add");

Route::set('GET', '/sign/{:[a-zA-Z0-9]+:}{:/?:}', "sign_controller:view");
Route::set('GET', '/sign{:/?:}', "sign_controller:browse");
Route::set('POST', '/sign{:/?:}', "sign_controller:add");
Route::set('PUT', '/sign/{:[a-zA-Z0-9]+:}{:/?:}', "sign_controller:sign");
