<?php

namespace svc\server;

trait ping
{
    public function ping()
    {
        return json_response([
            "sls_server_version" => SLS_VERSION,
            "php_version" => phpversion(),
            "operating_system" => PHP_OS,
            "server_info" => [
                "COLLECT_SENDER_NET_INFORMATION" => json_decode($_ENV["COLLECT_SENDER_NET_INFORMATION"]),
                "ALLOW_NOT_IDENTIFIED_SENDERS" => json_decode($_ENV["ALLOW_NOT_IDENTIFIED_SENDERS"]),
                "ALLOW_MESSAGE_DELETION" => json_decode($_ENV["ALLOW_MESSAGE_DELETION"]),
                "ALLOW_MESSAGE_EDIT" => json_decode($_ENV["ALLOW_MESSAGE_EDIT"]),
                "MESSAGE_MAX_SIZE" => json_decode($_ENV["MESSAGE_MAX_SIZE"])
            ]
        ]);
    }
}
