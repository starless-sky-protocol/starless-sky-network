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

namespace svc\identity;

function get_identity_info(array $public_keys): array|bool
{
    $response = [];
    if (!is_array($public_keys)) {
        add_message("error", "Invalid array data received.");
        return false;
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
                    ],
                    "attributes" => []
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
                    ],
                    "attributes" => []
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
                    "name" => $identity_data->public?->name ?? null,
                    "biography" => $identity_data->public?->biography ?? null
                ],
                "attributes" => $identity_data->private->attributes ?? []
            ];
        }
    }

    return $response;
}
