<?php

namespace svc\signing;

trait sign_request
{
    function sign_request($term)
    {
        $private_key = $GLOBALS["request"]->private_key;
        $signer_public_key = private_key_to_public_key($private_key);
        $signer_public_key_h = algo_gen_base34_hash($signer_public_key);
        $id = $GLOBALS["request"]->id;

        if (!is_private_key_valid($private_key)) {
            add_message("error", "Invalid private key.");
            return json_response();
        }
        if (!is_file($file_path = SIGNING_PATH . $signer_public_key_h . '/' . algo_gen_base34_hash($id))) {
            add_message("error", "Signing request not found");
            return json_response();
        }

        $sign_data = json_decode(decrypt_message(file_get_contents($file_path), $signer_public_key_h));

        $now = time();
        if($now + $sign_data->expires < $now) {
            add_message("error", "Sign request expired.");
            return json_response();
        }
        if(strtolower($term) != "view" && $sign_data->status->sign_status != null) {
            add_message("error", "Contract has already been declined or signed by it's receiver, so it is not possible to make changes to this contract.");
            return json_response();
        }

        $response = $sign_data;
        switch (strtolower($term)) {
            case "view":
                ;
                break;
            case "sign":
                $sign_data->status->sign_status = true;
                $sign_data->status->date = $now;
                $sign_json = json_encode($sign_data);
                file_put_contents($file_path, encrypt_message($sign_json, $signer_public_key_h));
                break;
            case "refuse":
                $sign_data->status->sign_status = false;
                $sign_data->status->date = $now;
                $sign_json = json_encode($sign_data);
                file_put_contents($file_path, encrypt_message($sign_json, $signer_public_key_h));
                break;
            default:
                add_message("error", "Invalid term.");
                return json_response();
        }

        add_message("info", "Signing action " . strtoupper($term) . " executed successfully.");

        return json_response($response);
    }
}
