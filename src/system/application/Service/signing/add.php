<?php

namespace svc\signing;

trait add
{
    function add()
    {
        $private_key = $GLOBALS["request"]->private_key;
        $issuer_public_key = algo_gen_hash($private_key, SLOPT_PRIVATE_KEY_TO_PUBLIC_KEY);
        $issuer_public_key_h = algo_gen_hash($issuer_public_key, SLOPT_PUBLIC_KEY_DIRNAME);
        $signer_public_key = $GLOBALS["request"]->public_key;
        $signer_public_key_h = algo_gen_hash($signer_public_key, SLOPT_PUBLIC_KEY_DIRNAME);
        $issuer_message = $GLOBALS["request"]->message;
        $sign_expires = $GLOBALS["request"]->expires;

        if (!is_private_key_valid($private_key)) {
            add_message("error", "Invalid issuer private key.");
            return json_response();
        }
        if (!is_public_key_valid($signer_public_key)) {
            add_message("error", "Invalid receiver public key.");
            return json_response();
        }
        if (strlen($issuer_message) > parse_hsize($size = config("information.sign_message_max_size"))) {
            add_message("error", "Sign message is greater than the server limit: " + $size);
            return json_response();
        }

        if (!is_dir($from_dir = CONTRACT_FROM_PATH . $issuer_public_key_h)) {
            mkdir($from_dir, 775);
        }
        if (!is_dir($to_dir = CONTRACT_TO_PATH . $signer_public_key_h)) {
            mkdir($to_dir, 775);
        }

        $id = gen_skyid();
        $now = time();
        $sign_data = [
            "id" => $id,
            "issued" => $now,
            "expires" => min($sign_expires, config("information.sign_max_expiration")),
            "message" => $issuer_message,
            "issuer" => [
                "public_key" => $issuer_public_key
            ],
            "signer" => [
                "public_key" => $signer_public_key
            ],
            "status" => [
                "sign_status" => null,
                "date" => null
            ]
        ];

        $sign_json = json_encode($sign_data);
        file_put_contents($from_dir . "/" . algo_gen_hash($id, SLOPT_SKYID_HASH), encrypt_message($sign_json, algo_gen_hash($issuer_public_key, SLOPT_PUBLIC_KEY_KEY)));
        file_put_contents($to_dir . "/" . algo_gen_hash($id, SLOPT_SKYID_HASH), encrypt_message($sign_json, algo_gen_hash($signer_public_key, SLOPT_PUBLIC_KEY_KEY)));

        add_message("info", "Signing request created successfully.");

        return json_response($sign_data);
    }
}
