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
            "server_info" => config("information")
        ]);
    }
}
