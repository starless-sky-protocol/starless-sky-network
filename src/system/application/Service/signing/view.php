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

namespace svc\signing;

function view(string $private_key, string $id)
{
    $private_key = load_from_private($private_key);
    if ($private_key == false) {
        add_message("error", "Invalid or not authenticated private key received");
        return false;
    }

    $public_key = $private_key->getPublicKey();
    $public_key_h = algo_gen_hash($public_key->toString("PKCS8"), SLOPT_PUBLIC_KEY_ADDRESS);
    $public_key_d = algo_gen_hash($public_key_h, SLOPT_PUBLIC_KEY_DIRNAME);

    $id_h = algo_gen_hash($id, SLOPT_SKYID_HASH);

    $path = CONTRACT_FROM_PATH;
    tryagain:
    if (!is_file($file_path = $path . $public_key_d . '/' . $id_h)) {
        if ($path == CONTRACT_FROM_PATH) {
            $path = CONTRACT_TO_PATH;
            goto tryagain;
        } else {
            add_message("error", "Signing request not found");
            return false;
        }
    }

    $sign_data = json_decode(decrypt_message(file_get_contents($file_path), ""));

    $puk = load_from_public_hash($path == CONTRACT_TO_PATH ? $sign_data->issuer->public_key : $sign_data->signer->public_key);
    if ($puk == false) {
        add_message("warn", "Contract invalided: the other point public key is not valid on this network.");
        return false;
    }

    $sharedKey = shared_key($private_key, $puk);
    $sign_data->id = decrypt_message($sign_data->id, $sharedKey);
    $sign_data->message = decrypt_message($sign_data->message, $sharedKey);
    $sign_data->title = decrypt_message($sign_data->title, $sharedKey);
    $sign_data->status = json_decode(decrypt_message($sign_data->status, $sharedKey));

    $now = time();
    if ($now + $sign_data->expires < $now && $sign_data->status->sign_status == null) {
        add_message("info", "The receiver did not sign the contract on time.");
    }
    if ($sign_data->status->sign_status !== null) {
        add_message("info", "Contract has already been declined or signed by it's receiver.");
    }

    return $sign_data;
}
