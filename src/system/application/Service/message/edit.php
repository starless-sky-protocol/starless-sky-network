<?php

namespace svc\message;

trait edit
{
    public function edit($id)
    {
        if (json_decode($_ENV["ALLOW_MESSAGE_EDIT"]) == false) {
            add_message("error", "This SLS network doens't allow editing of messages.");
            return json_response();
        }

        $message = $GLOBALS["request"]->message;
        $public_key = $GLOBALS["request"]->public_key;
        $private_key = $GLOBALS["request"]->private_key;
        $public_key_h = SLS_HASH_PREFIX . algo_gen_base34_hash($public_key);

        if (!is_public_key_valid($public_key)) {
            add_message("error", "Invalid public key.");
            return json_response();
        }

        if (!is_private_key_valid($private_key)) {
            add_message("error", "Invalid private key.");
            return json_response();
        }

        if (!is_dir($public_key_d = MESSAGES_PATH . $public_key_h)) {
            add_message("error", "Message not found");
            return json_response();
        }

        if (!is_file($file_path = $public_key_d . "/" . SLS_HASH_PREFIX . algo_gen_base34_hash($id))) {
            add_message("error", "Message not found");
            return json_response();
        }

        $message_content = file_get_contents($file_path);
        $message_decrypted = json_decode(decrypt_message($message_content, $public_key_h));

        if (secure_strcmp($message_decrypted->pair->sender_public_key, $sender_public_key = private_key_to_public_key($private_key))) {
            add_message("error", "The private key does not match with the sender's private key of the message.");
            return json_response();
        }

        $now = time();
        $message_x = [
            "id" => $message_decrypted->id,
            "content" => $message->content,
            "subject" => $message->subject,
            "manifest" => [
                "created_at" => $message_decrypted->manifest->created_at,
                "updated_at" => $now,
                "is_modified" => true,
                "message_blake3_digest" => blake3($message->content . $message->subject)
            ],
            "pair" => [
                "sender_public_key" => $sender_public_key,
                "receiver_public_key" => $public_key
            ]
        ];

        $message_json_data = encrypt_message(json_encode($message_x), $public_key_h);

        file_put_contents($public_key_d . "/" . SLS_HASH_PREFIX . algo_gen_base34_hash($id), $message_json_data);

        add_message("info", "Message edited using sender's private key");
        return json_response(
            [
                "pair" => [
                    "sender_public_key" => $message_decrypted->sender->public_key,
                    "receiver_public_key" => $public_key
                ],
                "message_length" => strlen($message_json_data),
                "id" => $id,
                "message_blake3_digest" => blake3($message->content . $message->subject)
            ]
        );
    }
}
