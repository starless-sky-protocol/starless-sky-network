<?php

namespace svc\signing;

trait view
{
    function view($id)
    {
        $private_key = $GLOBALS["request"]->private_key;
        $issuer_public_key = algo_gen_hash($private_key, SLOPT_PRIVATE_KEY_TO_PUBLIC_KEY);
        $issuer_public_key_h = algo_gen_hash($issuer_public_key, SLOPT_PUBLIC_KEY_DIRNAME);

        if (!is_hash_valid($private_key)) {
            add_message("error", "Invalid private key.");
            return json_response();
        }
        $path = CONTRACT_FROM_PATH;
        tryagain:
        if (!is_file($file_path = $path . $issuer_public_key_h . '/' . algo_gen_hash($id, SLOPT_SKYID_HASH))) {
            if ($path == CONTRACT_FROM_PATH) {
                $path = CONTRACT_TO_PATH;
                goto tryagain;
            } else {
                add_message("error", "Signing request not found");
                return json_response();
            }
        }

        $sign_data = json_decode(decrypt_message(file_get_contents($file_path), algo_gen_hash($issuer_public_key, SLOPT_PUBLIC_KEY_SECRET)));

        $now = time();
        if ($now + $sign_data->expires < $now && $sign_data->status->sign_status == null) {
            add_message("info", "The receiver did not sign the contract on time.");
        }
        if ($sign_data->status->sign_status !== null) {
            add_message("info", "Contract has already been declined or signed by it's receiver.");
        }

        $response = $sign_data;

        return json_response($response);
    }
}
