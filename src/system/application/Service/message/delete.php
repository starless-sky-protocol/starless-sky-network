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

namespace svc\message;

function delete(string $private_key, string $id)
{
    if (config("information.allow_message_deletion") == false) {
        add_message("error", "This SLS network doens't allow deletion of messages.");
        return false;
    }

    $private_key_raw = $private_key;
    $private_key = load($private_key_raw);
    if ($private_key == false) {
        add_message("error", "Invalid private key received");
        return false;
    }
    
    $public_key = $private_key->getPublicKey();
    $public_key_h = algo_gen_hash($public_key->toString("PKCS8"), SLOPT_PUBLIC_KEY_ADDRESS);
    $public_key_d = algo_gen_hash($public_key_h, SLOPT_PUBLIC_KEY_DIRNAME);
    $id_h = algo_gen_hash($id, SLOPT_SKYID_HASH);

    $dir = SENT_PATH;
    tryagain:
    if (!is_dir($fullpath = $dir . $public_key_d)) {
        if ($dir == SENT_PATH) {
            $dir = INBOX_PATH;
            goto tryagain;
        }
    }

    $file_path = $fullpath . "/" . $id_h;
    if (!is_file($file_path)) {
        if ($dir == SENT_PATH) {
            $dir = INBOX_PATH;
            goto tryagain;
        } else {
            add_message("error", "Message not found");
            return false;
        }
    }

    $message_content = file_get_contents($file_path);
    $message_decrypted = json_decode(decrypt_message($message_content, ""));

    if ($dir == INBOX_PATH) { // found message on inbox folder, so he received it
        // delete for receiver
        $file = INBOX_PATH . $public_key_d . "/" . $id_h;
        unlink($file);
    } else { // found message on his sent folder, so he sent it
        // delete for sender
        $file = SENT_PATH . $public_key_d . "/" . $id_h;
        unlink($file);

        // delete for receivers
        $storeTo = $message_decrypted->pair->to;
        foreach ($storeTo as $to_public_key) {
            $to_public_key_h = algo_gen_hash($to_public_key, SLOPT_PUBLIC_KEY_DIRNAME);
            $file = INBOX_PATH . $to_public_key_h . "/" . $id_h;
            if (is_file($file) /*check if file wasn't already deleted by receiver*/) unlink($file);
        }
    }

    finish:
    add_message("info", "Message successfully deleted");
    return true;
}
