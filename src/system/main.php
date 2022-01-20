<?php
use Inphinit\Routing\Route;

Route::set('GET', '/ping', "ServerController:Ping");
Route::set('GET', '/generate-keypair', "PrivateKeyController:GetRandomPrivateKey");

Route::set('GET', '/messages', "MessagesController:Browse");
Route::set('GET', '/messages/receiver/{:[a-zA-Z0-9]+:}{:/?:}', "MessagesController:ReadFromReceiver");
Route::set('GET', '/messages/sender/{:[a-zA-Z0-9]+:}{:/?:}', "MessagesController:ReadFromSender");
Route::set('PUT', '/messages/{:[a-zA-Z0-9]+:}{:/?:}', "MessagesController:Edit");
Route::set('DELETE', '/messages/receiver/{:[a-zA-Z0-9]+:}{:/?:}', "MessagesController:DeleteFromReceiver");
Route::set('DELETE', '/messages/sender/{:[a-zA-Z0-9]+:}{:/?:}', "MessagesController:DeleteFromSender");
Route::set('POST', '/messages', "MessagesController:Add");