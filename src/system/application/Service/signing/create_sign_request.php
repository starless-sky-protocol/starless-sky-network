<?php

namespace svc\signing;

trait create_sign_request
{
    function create_sign_request()
    {
        $private_key = $GLOBALS["request"]->private_key;
        $issuer_public_key = private_key_to_public_key($private_key);
        $signer_public_key = $GLOBALS["request"]->public_key;
        $signer_public_key_h = algo_gen_base34_hash($signer_public_key);
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

        if (!is_dir($public_key_d = SIGNING_PATH . $signer_public_key_h)) {
            mkdir($public_key_d, 775);
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
        file_put_contents($public_key_d . "/" . algo_gen_base34_hash($id), encrypt_message($sign_json, $signer_public_key_h));

        add_message("info", "Signing request created successfully.");

        return json_response($sign_data);
    }
}
