<?php

namespace svc\signing;

trait issuer_view_contract
{
    function issuer_view_contract()
    {
        $private_key = $GLOBALS["request"]->private_key;
        $signer_public_key = $GLOBALS["request"]->public_key;
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
        if($now + $sign_data->expires < $now && $sign_data->status->sign_status == null) {
            add_message("info", "The receiver did not sign the contract on time.");
        }
        if($sign_data->status->sign_status !== null) {
            add_message("info", "Contract has already been declined or signed by it's receiver.");
        }

        $response = $sign_data;

        return json_response($response);
    }
}
