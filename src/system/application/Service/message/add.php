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

        $public_key_h = algo_gen_hash($public_key, SLOPT_PUBLIC_KEY_DIRNAME);

        if (!is_dir($public_key_d = INBOX_PATH . $public_key_h)) {
            mkdir($public_key_d, 775);
        }

        $sender_public_key = null;
        if (null !== ($private_key = $GLOBALS["request"]->private_key) & $private_key != "") {
            if (!is_private_key_valid($private_key)) {
                add_message("error", "Invalid private key.");
                return json_response();
            }

            $sender_public_key = algo_gen_hash($private_key, SLOPT_PRIVATE_KEY_TO_PUBLIC_KEY);

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

        if (trim($message->content) == "" || trim($message->subject) == "") {
            add_message("error", "Message contents cannot be empty or whitespace.");
            return json_response();
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
                "from" => $sender_public_key,
                "to" => $public_key
            ]
        ];

        $message_json_data_for_receiver = encrypt_message(json_encode($message_x), algo_gen_hash($public_key, SLOPT_PUBLIC_KEY_KEY));

        if (strlen($message->content . $message->subject) >= parse_hsize($size = config("information.message_max_size"))) {
            add_message("error", "Message content cannot be bigger than " . $size . " bytes.");
            return json_response();
        }

        file_put_contents($public_key_d . "/" . algo_gen_hash($id, SLOPT_SKYID_HASH), $message_json_data_for_receiver);

        if ($sender_public_key != null) {
            $sender_public_key_h = algo_gen_hash($sender_public_key, SLOPT_PUBLIC_KEY_DIRNAME);
            if (!is_dir($public_key_d = SENT_PATH . $sender_public_key_h)) {
                mkdir($public_key_d, 775);
            }
            $message_json_data_for_sender = encrypt_message(json_encode($message_x), algo_gen_hash($sender_public_key, SLOPT_PUBLIC_KEY_KEY));
            file_put_contents(SENT_PATH . $sender_public_key_h . "/" . algo_gen_hash($id, SLOPT_SKYID_HASH), $message_json_data_for_sender);
        } else {
            add_message("warn", "Could not store transaction for sender as it is sending anonymously.");
        }

        add_message("info", "Message inserted at public key");
        return json_response(
            [
                "pair" => [
                    "from" => $sender_public_key,
                    "to" => $public_key
                ],
                "message_length" => hsize(strlen($message->content . $message->subject)),
                "id" => $id,
                "message_blake3_digest" => blake3($message->content . $message->subject)
            ]
        );
    }
}
