<?php

set_error_handler(function($errno, $errstr, $errfile, $errline ) {
    http_response_code(500);

    $debug = json_decode($_ENV["DEBUG_ENABLED"]);

    if(!$debug && strpos($errstr, $undefined_property_text = "Undefined property: stdClass::$") == 0) {
        add_message("error", "Missing request parameter: " . str_replace($undefined_property_text, "", $errstr));
        json_response([], true);
        die();
    }

    if($debug) {
        add_message("fatal", "A fatal system error occurred and it the connection had to be dropped. See details below.");
        json_response([
            "error_no" => $errno,
            "error_str" => $errstr,
            "error_file" => $errfile,
            "error_line" => $errline
        ], true);
        die();
    } else {
        add_message("fatal", "A fatal system error occurred and it the connection had to be dropped.");
        json_response([], true);
        die();
    }
});