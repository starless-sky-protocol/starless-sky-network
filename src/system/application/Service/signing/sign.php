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

function sign(string $signer_private_key, string $term, string $contract_id)
{
    $private_key = load_from_private($signer_private_key);
    if ($private_key == false) {
        add_message("error", "Invalid or not authenticated private key received");
        return false;
    }

    $signer_public_key_obj = $private_key->getPublicKey();
    $signer_public_key_hash = algo_gen_hash($signer_public_key_obj->toString("PKCS8"), SLOPT_PUBLIC_KEY_ADDRESS);
    $signer_public_key_h = algo_gen_hash($signer_public_key_hash, SLOPT_PUBLIC_KEY_DIRNAME);

    $id_h = algo_gen_hash($contract_id, SLOPT_SKYID_HASH);

    $to_path = CONTRACT_TO_PATH . $signer_public_key_h . '/' . $id_h;
    if (!is_file($to_path)) {
        add_message("error", "Signing request not found");
        return false;
    }

    $sign_data = json_decode(decrypt_message(file_get_contents($to_path), ""));

    $puk = load_from_public_hash($sign_data->issuer->public_key);
    if ($puk == false) {
        add_message("warn", "Contract invalided: the issuer's public key is not valid on this network.");
        return false;
    }

    $sharedKey = shared_key($private_key, $puk);
    $sign_data->id = decrypt_message($sign_data->id, $sharedKey);
    $sign_data->message = decrypt_message($sign_data->message, $sharedKey);
    $sign_data->status = json_decode(decrypt_message($sign_data->status, $sharedKey));

    $from_path = CONTRACT_FROM_PATH . algo_gen_hash($sign_data->issuer->public_key, SLOPT_PUBLIC_KEY_DIRNAME) . '/' . $id_h;

    $now = time();
    if ($now + $sign_data->expires < $now) {
        add_message("error", "Sign request expired.");
        return false;
    }
    if ($sign_data->status->sign_status !== null) {
        add_message("error", "Contract has already been declined or signed by it's receiver, so it is not possible to make changes to this contract.");
        return false;
    }

    switch (strtolower($term)) {
        case "sign":
            $sign_data_b = $sign_data;
            $sign_data_b->status->sign_status = true;
            $sign_data_b->status->date = $now;

            $sign_data_b->id = encrypt_message($sign_data->id, $sharedKey);
            $sign_data_b->message = encrypt_message($sign_data->message, $sharedKey);
            $sign_data_b->title = encrypt_message($sign_data->title, $sharedKey);
            $sign_data_b->status = encrypt_message(json_encode($sign_data->status), $sharedKey);

            $sign_json = json_encode($sign_data_b);
            file_put_contents($from_path, encrypt_message($sign_json, ""));
            file_put_contents($to_path, encrypt_message($sign_json, ""));
            break;
        case "refuse":
            $sign_data_b = $sign_data;
            $sign_data_b->status->sign_status = false;
            $sign_data_b->status->date = $now;

            $sign_data_b->id = encrypt_message($sign_data->id, $sharedKey);
            $sign_data_b->message = encrypt_message($sign_data->message, $sharedKey);
            $sign_data_b->title = encrypt_message($sign_data->title, $sharedKey);
            $sign_data_b->status = encrypt_message(json_encode($sign_data->status), $sharedKey);

            $sign_json = json_encode($sign_data_b);
            file_put_contents($from_path, encrypt_message($sign_json, ""));
            file_put_contents($to_path, encrypt_message($sign_json, ""));
            break;
        default:
            add_message("error", "Invalid term.");
            return false;
    }

    create_transaction(
        "contract.decide",
        $signer_public_key_hash,
        $sign_data_b->issuer->public_key,
        $contract_id,
        $sign_data_b->message . $sign_data_b->title . $sign_data->issuer->public_key . $signer_public_key_hash . $sign_data_b->expires . $sign_data_b->issued . json_encode($sign_data->status)
    );

    add_message("info", "Signing action " . strtoupper($term) . " executed successfully.");

    return $sign_data_b;
}
