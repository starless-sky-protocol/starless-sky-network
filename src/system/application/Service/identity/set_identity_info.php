<?php

namespace svc\identity;

trait set_identity_info
{
    public function set_identity_info()
    {
        $private_key = $GLOBALS["request"]->private_key;
        $public_key = private_key_to_public_key($private_key);
        $public_key_h = algo_gen_base34_hash($public_key);

        if (!is_private_key_valid($private_key)) {
            add_message("error", "Invalid private key.");
            return json_response();
        }

        $identity_data = [
            "public" => [
                "name" => @$GLOBALS["request"]->public->name ?? "",
                "biography" => @$GLOBALS["request"]->public->biography ?? ""
            ],
            "private" => [
                "last_modify_date" => time()
            ]
        ];

        $rawData = json_encode($identity_data);

        if (strlen($rawData) >= $max_size = json_decode($_ENV["MESSAGE_MAX_SIZE"])) {
            add_message("error", "Information content cannot be bigger than $max_size bytes.");
            return json_response();
        }

        $encrypted = encrypt_message($rawData, $public_key);

        file_put_contents(IDENTITY_PATH . $public_key_h, $encrypted);

        add_message("info", "Network Identity info modified");

        return json_response($identity_data["public"]);
    }
}
