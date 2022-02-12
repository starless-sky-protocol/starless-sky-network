<?php

namespace svc\identity;

trait get_identity_info
{
    public function get_identity_info()
    {
        $public_keys = $GLOBALS["request"]->public_keys;

        $response = [];
        if (!is_array($public_keys)) {
            add_message("error", "Invalid array data received.");
            return json_response();
        } else {
            foreach ($public_keys as $public_key) {
                $public_key_h = algo_gen_hash($public_key, SLOPT_PUBLIC_KEY_DIRNAME);
                if (!is_hash_valid($public_key)) {
                    $response[] = [
                        "public_key" => $public_key,
                        "status" => "invalid",
                        "identity" => [
                            "name" => null,
                            "biography" => null
                        ]
                    ];
                    continue;
                }
                if (!is_file(IDENTITY_PATH . $public_key_h)) {
                    $response[] = [
                        "public_key" => $public_key,
                        "status" => "not_found",
                        "identity" => [
                            "name" => null,
                            "biography" => null
                        ]
                    ];
                    continue;
                }

                $encryptedData = file_get_contents(IDENTITY_PATH . $public_key_h);
                $raw = decrypt_message($encryptedData, algo_gen_hash($public_key, SLOPT_PUBLIC_KEY_SECRET));
                $identity_data = json_decode($raw);

                $response[] = [
                    "public_key" => $public_key,
                    "status" => "found",
                    "identity" => [
                        "name" => $identity_data->public->name,
                        "biography" => $identity_data->public->biography
                    ]
                ];
            }
        }

        return json_response($response);
    }
}
