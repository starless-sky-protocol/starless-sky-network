<?php

use Inphinit\Http\Response;

$GLOBALS["success"] = true;
$GLOBALS["messages"] = [];

$__input = file_get_contents('php://input');
if (!empty($__input)) {
    $GLOBALS["request"] = json_decode($__input, false);
    if ($GLOBALS["request"] == null) {
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

function json_response(?array $content = [], bool $close_connection = false): string
{
    Response::type('application/json');

    if($GLOBALS["success"] == false) {
        http_response_code(400);
    } else {
        http_response_code(200);
    }

    $json_response = json_encode([
        'success' => $GLOBALS["success"],
        'messages' => array_reverse($GLOBALS["messages"]),
        'response' => $content
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
