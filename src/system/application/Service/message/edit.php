<?php

namespace svc\message;

trait edit
{
    public function edit($id)
    {
        if (config("information.allow_message_edit") == false) {
            add_message("error", "This SLS network doens't allow editing of messages.");
            return json_response();
        }

        $message = $GLOBALS["request"]->message;
        $private_key = $GLOBALS["request"]->private_key;
        $public_key = algo_gen_hash($private_key, SLOPT_PRIVATE_KEY_TO_PUBLIC_KEY);
        $public_key_h = algo_gen_hash($public_key, SLOPT_PUBLIC_KEY_DIRNAME);

        if (!is_public_key_valid($public_key)) {
            add_message("error", "Invalid public key.");
            return json_response();
        }

        if (!is_private_key_valid($private_key)) {
            add_message("error", "Invalid private key.");
            return json_response();
        }

        if (!is_dir($public_key_d = SENT_PATH . $public_key_h)) {
            add_message("error", "Message not found");
            return json_response();
        }

        if (!is_file($file_path = $public_key_d . "/" . algo_gen_hash($id, SLOPT_SKYID_HASH))) {
            add_message("error", "Message not found");
            return json_response();
        }

        $message_content = file_get_contents($file_path);
        $message_decrypted = json_decode(decrypt_message($message_content, algo_gen_hash($public_key, SLOPT_PUBLIC_KEY_KEY)));

        if (!secure_strcmp($message_decrypted->pair->from, $sender_public_key = algo_gen_hash($private_key, SLOPT_PRIVATE_KEY_TO_PUBLIC_KEY))) {
            add_message("error", "The private key does not match with the sender's private key of the message.");
            return json_response();
        }

        if (trim($message->content) == "" || trim($message->subject) == "") {
            add_message("error", "Message contents cannot be empty or whitespace.");
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
                "from" => $sender_public_key,
                "to" => $public_key
            ]
        ];

        $message_json_data = encrypt_message(json_encode($message_x), algo_gen_hash($public_key, SLOPT_PUBLIC_KEY_KEY));

        if (strlen($message->content . $message->subject) >= parse_hsize($size = config("information.message_max_size"))) {
            add_message("error", "Message content cannot be bigger than " . $size . " bytes.");
            return json_response();
        }

        file_put_contents($public_key_d . "/" . algo_gen_hash($id, SLOPT_SKYID_HASH), $message_json_data);
        $sender_public_key_h = algo_gen_hash($sender_public_key, SLOPT_PUBLIC_KEY_DIRNAME);
        $message_json_data_for_sender = encrypt_message(json_encode($message_x), algo_gen_hash($sender_public_key, SLOPT_PUBLIC_KEY_KEY));
        file_put_contents(SENT_PATH . $sender_public_key_h . "/" . algo_gen_hash($id, SLOPT_SKYID_HASH), $message_json_data_for_sender);

        add_message("info", "Message edited using sender's private key");
        return json_response(
            [
                "pair" => $message_decrypted->pair,
                "message_length" => hsize(strlen($message->content . $message->subject)),
                "id" => $id,
                "message_blake3_digest" => $message_x["manifest"]["message_blake3_digest"]
            ]
        );
    }
}
