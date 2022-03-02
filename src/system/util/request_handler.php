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

use Inphinit\Http\Response;

$GLOBALS["success"] = true;
$GLOBALS["messages"] = [];

$__input = file_get_contents('php://input');
if (!empty($__input)) {
    $GLOBALS["request"] = json_decode($__input, false);
    if ($GLOBALS["request"] == null && $_SERVER['REQUEST_METHOD'] != "GET") {
        add_message("error", "Invalid JSON data received");
        json_response([], true);
        die();
    }
}

function safe_get_useragent()
{
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
        return "unknown";
    } else {
        return $_SERVER['HTTP_USER_AGENT'];
    }
}

function add_message(string $type, string $message)
{
    $GLOBALS["messages"][] = [
        "level" => $type,
        "message" => $message
    ];
    if ($type == "error" || $type == "fatal") {
        $GLOBALS["success"] = false;
    }
}

function json_response($content = null, bool $close_connection = false)
{
    Response::type('application/json');

    if ($GLOBALS["success"] == false) {
        http_response_code(400);
    } else {
        http_response_code(200);
    }

    $json_response = json_encode([
        'success' => $GLOBALS["success"],
        'messages' => array_reverse($GLOBALS["messages"]),
        'response' => $content,
        'transaction' => $GLOBALS["transaction"] ?? null
    ]);

    if (!$close_connection) {
        return $json_response;
    } else {
        ob_end_clean();
        header("Connection: close");
        ignore_user_abort(true);
        ob_start();
        echo $json_response;
        $size = ob_get_length();
        header("Content-Length: $size");
        ob_end_flush();
        flush();
    }
}
