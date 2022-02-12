<?php

namespace svc\signing;

trait sign
{
    function sign($id)
    {
        $private_key = $GLOBALS["request"]->private_key;
        $signer_public_key = algo_gen_hash($private_key, SLOPT_PRIVATE_KEY_TO_PUBLIC_KEY);
        $signer_public_key_h = algo_gen_hash($signer_public_key, SLOPT_PUBLIC_KEY_DIRNAME);
        $term = $GLOBALS["request"]->term;

        if (!is_hash_valid($private_key)) {
            add_message("error", "Invalid private key.");
            return json_response();
        }

        $to_path = CONTRACT_TO_PATH . $signer_public_key_h . '/' . algo_gen_hash($id, SLOPT_SKYID_HASH);
        if (!is_file($to_path)) {
            add_message("error", "Signing request not found");
            return json_response();
        }

        $to_symetric_key = algo_gen_hash($signer_public_key, SLOPT_PUBLIC_KEY_SECRET);
        $sign_data = json_decode(decrypt_message(file_get_contents($to_path), $to_symetric_key));
        $from_path = CONTRACT_FROM_PATH . algo_gen_hash($sign_data->issuer->public_key, SLOPT_PUBLIC_KEY_DIRNAME) . '/' . algo_gen_hash($id, SLOPT_SKYID_HASH);
        $from_symetric_key = algo_gen_hash($sign_data->issuer->public_key, SLOPT_PUBLIC_KEY_SECRET);

        $now = time();
        if ($now + $sign_data->expires < $now) {
            add_message("error", "Sign request expired.");
            return json_response();
        }
        if (strtolower($term) != "view" && $sign_data->status->sign_status != null) {
            add_message("error", "Contract has already been declined or signed by it's receiver, so it is not possible to make changes to this contract.");
            return json_response();
        }

        $response = $sign_data;
        switch (strtolower($term)) {
            case "sign":
                $sign_data->status->sign_status = true;
                $sign_data->status->date = $now;
                $sign_json = json_encode($sign_data);
                file_put_contents($from_path, encrypt_message($sign_json, $from_symetric_key));
                file_put_contents($to_path, encrypt_message($sign_json, $to_symetric_key));
                break;
            case "refuse":
                $sign_data->status->sign_status = false;
                $sign_data->status->date = $now;
                $sign_json = json_encode($sign_data);
                file_put_contents($from_path, encrypt_message($sign_json, $from_symetric_key));
                file_put_contents($to_path, encrypt_message($sign_json, $to_symetric_key));
                break;
            default:
                add_message("error", "Invalid term.");
                return json_response();
        }

        add_message("info", "Signing action " . strtoupper($term) . " executed successfully.");

        return json_response($response);
    }
}
