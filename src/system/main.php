<?php
use Inphinit\Routing\Route;

Route::set('GET', '/ping', "server_controller:ping");

Route::set('GET', '/identity/generate-keypair', "identity_controller:generate_random_keypair");
Route::set('POST', '/identity{:/?:}', "identity_controller:set_identity_info");
Route::set('GET', '/identity{:/?:}', "identity_controller:get_identity_info");
Route::set('DELETE', '/identity{:/?:}', "identity_controller:delete_identity_info");

Route::set('GET', '/messages{:/?:}', "messages_controller:browse");
Route::set('GET', '/messages/receiver/{:[a-zA-Z0-9]+:}{:/?:}', "messages_controller:read_from_receiver");
Route::set('GET', '/messages/sender/{:[a-zA-Z0-9]+:}{:/?:}', "messages_controller:read_from_sender");
Route::set('PUT', '/messages/{:[a-zA-Z0-9]+:}{:/?:}', "messages_controller:edit");
Route::set('DELETE', '/messages/receiver/{:[a-zA-Z0-9]+:}{:/?:}', "messages_controller:delete_from_receiver");
Route::set('DELETE', '/messages/sender/{:[a-zA-Z0-9]+:}{:/?:}', "messages_controller:delete_from_sender");
Route::set('POST', '/messages{:/?:}', "messages_controller:add");

Route::set('GET', '/sign{:/?:}', "sign_controller:issuer_view_contract");
Route::set('POST', '/sign{:/?:}', "sign_controller:create_sign_request");
Route::set('POST', '/sign/{:[a-zA-Z]+:}{:/?:}', "sign_controller:sign_request");