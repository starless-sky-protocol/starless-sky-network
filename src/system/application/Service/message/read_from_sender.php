<?php

namespace svc\message;

trait read_from_sender
{
    public function read_from_sender($id)
    {
        $private_key = $GLOBALS["request"]->private_key;
        $public_key = $GLOBALS["request"]->public_key;
        $public_key_h = algo_gen_base34_hash($public_key);

        if (!is_private_key_valid($private_key)) {
            add_message("error", "Invalid private key.");
            return json_response();
        }

        if (!is_public_key_valid($public_key)) {
            add_message("error", "Invalid public key.");
            return json_response();
        }

        if (!is_dir($public_key_d = MESSAGES_PATH . $public_key_h)) {
            add_message("error", "Message not found");
            return json_response();
        }

        if (!is_file($file_path = $public_key_d . "/" . algo_gen_base34_hash($id))) {
            add_message("error", "Message not found");
            return json_response();
        }

        $message_content = file_get_contents($file_path);
        $message_decrypted = json_decode(decrypt_message($message_content, $public_key_h));

        if (!secure_strcmp($message_decrypted->pair->sender_public_key, private_key_to_public_key($private_key))) {
            add_message("error", "The private key does not match with the sender's private key of the message.");
            return json_response();
        }

        $data = [
            "id" => $message_decrypted->id,
            "manifest" => $message_decrypted->manifest,
            "pair" => $message_decrypted->pair,
            "size" => hsize(strlen($message_content)),
            "message" => [
                "subject" => $message_decrypted->subject,
                "content" => $message_decrypted->content,
            ]
        ];

        add_message("info", "Message data delivered to sender's client");

        return json_response($data);
    }
}