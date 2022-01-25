<?php

namespace svc\message;

trait add
{
    public function add()
    {
        $public_key = ($GLOBALS["request"]->public_key);
        $message = $GLOBALS["request"]->message;

        if (!is_public_key_valid($public_key)) {
            add_message("error", "Invalid public key.");
            return json_response();
        }

        $public_key_h = algo_gen_base34_hash($public_key);

        if (!is_dir($public_key_d = MESSAGES_PATH . $public_key_h)) {
            mkdir($public_key_d, 775);
        }

        $sender_public_key = null;
        if (null !== ($private_key = $GLOBALS["request"]->private_key) & $private_key != "") {
            if (!is_private_key_valid($private_key)) {
                add_message("error", "Invalid private key.");
                return json_response();
            }

            $sender_public_key = private_key_to_public_key($private_key);

            if (!is_public_key_valid($sender_public_key)) {
                add_message("error", "Invalid sender public key.");
                return json_response();
            }
            if (secure_strcmp($public_key, $sender_public_key) == true) {
                add_message("error", "Sender public key cannot be the same as the target public key.");
                return json_response();
            }
        } else {
            if (!config("information.allow_not_identified_senders")) {
                add_message("error", "This network does not allow sending messages by users not identified with a public key.");
                return json_response();
            }
        }

        $now = time();
        $id = gen_skyid();
        $message_x = [
            "id" => $id,
            "content" => $message->content,
            "subject" => $message->subject,
            "manifest" => [
                "created_at" => $now,
                "updated_at" => $now,
                "is_modified" => false,
                "message_blake3_digest" => blake3($message->content . $message->subject)
            ],
            "pair" => [
                "sender_public_key" => $sender_public_key,
                "receiver_public_key" => $public_key
            ]
        ];

        $message_json_data = encrypt_message(json_encode($message_x), $public_key_h);

        if (strlen($message_json_data) >= parse_hsize($size = config("information.message_max_size"))) {
            add_message("error", "Message content cannot be bigger than " . $size . " bytes.");
            return json_response();
        }

        file_put_contents($public_key_d . "/" . algo_gen_base34_hash($id), $message_json_data);

        add_message("info", "Message inserted at public key");
        return json_response(
            [
                "pair" => [
                    "sender_public_key" => $sender_public_key,
                    "receiver_public_key" => $public_key
                ],
                "message_length" => strlen($message_json_data),
                "id" => $id,
                "message_blake3_digest" => blake3($message->content . $message->subject)
            ]
        );
    }
}
