<?php

namespace svc\message;

DEFINE("MESSAGE_ADD_COMMAND", "message.send");

trait add
{
    public function add()
    {
        $private_key_raw = $GLOBALS["request"]->private_key;
        $private_key = load($private_key_raw);
        $public_keys = $GLOBALS["request"]->public_keys;
        $message = $GLOBALS["request"]->message;

        $sender_public_key_obj = $private_key->getPublicKey();
        $sender_public_key_hash = algo_gen_hash($sender_public_key_obj->toString("PKCS8"), SLOPT_PUBLIC_KEY_ADDRESS);
        $sender_public_key_h = algo_gen_hash($sender_public_key_hash, SLOPT_PUBLIC_KEY_DIRNAME);

        if (!is_array($public_keys)) {
            add_message("error", "'public_keys' must be an array of public keys.");
            return json_response();
        } else {
            if (count($public_keys) == 0) {
                add_message("error", "There are no recipients in your submission.");
                return json_response();
            }
            if (count($public_keys) > $max = config("information.multicast_max_receivers")) {
                add_message("error", "Attempting to send messages to more recipients than the network allows ($max).");
                return json_response();
            }
            if (trim($message->content) == "" || trim($message->subject) == "") {
                add_message("error", "Message contents cannot be empty or whitespace.");
                return json_response();
            }
            if (strlen($message->content . $message->subject) >= parse_hsize($size = config("information.message_max_size"))) {
                add_message("error", "Message content cannot be bigger than " . $size . " bytes.");
                return json_response();
            }

            $now = time();
            $id = gen_skyid();
            $id_h = algo_gen_hash($id, SLOPT_SKYID_HASH);
            $message_x = [
                "id" => $id,
                "content" => $message->content,
                "subject" => $message->subject,
                "read" => false,
                "manifest" => [
                    "created_at" => $now,
                    "updated_at" => $now,
                    "is_modified" => false,
                    "message_blake3_digest" => blake3($message->content . $message->subject)
                ],
                "pair" => [
                    "from" => $sender_public_key_hash,
                    "to" => $public_keys
                ]
            ];

            if (!is_dir($b_path = SENT_PATH . $sender_public_key_h)) {
                mkdir($b_path, 775);
            }

            $sharedKey = $private_key->toString("PKCS8");
            $message_y = $message_x;
            $message_y["id"] = encrypt_message($message_x["id"], $sharedKey);
            $message_y["manifest"] = encrypt_message(json_encode($message_x["manifest"]), $sharedKey);
            $message_y["content"] = encrypt_message($message->content, $sharedKey);
            $message_y["subject"] = encrypt_message($message->subject, $sharedKey);

            $message_json_data_for_sender = encrypt_message(json_encode($message_y), "");
            file_put_contents($b_path . "/" . $id_h, $message_json_data_for_sender);

            $sent = 0;
            foreach ($public_keys as $public_key) {
                $public_key_h = algo_gen_hash($public_key, SLOPT_PUBLIC_KEY_DIRNAME);

                if (strcmp($public_key, $sender_public_key_hash) == 0) {
                    add_message("warn", "Sender public key cannot be the same as the target public key. Ignoring this receiver.");
                    continue;
                }

                if (!is_hash_valid($public_key)) {
                    add_message("warn", "Received invalid hash. Ignoring this receiver.");
                    continue;
                }

                if (!is_dir($b_path = INBOX_PATH . $public_key_h)) {
                    mkdir($b_path, 775);
                }

                $sharedKey = shared_key($private_key, load_from_public_hash($public_key));

                $message_y = $message_x;

                $message_y["id"] = encrypt_message($message_x["id"], $sharedKey);
                $message_y["manifest"] = encrypt_message(json_encode($message_x["manifest"]), $sharedKey);
                $message_y["content"] = encrypt_message($message->content, $sharedKey);
                $message_y["subject"] = encrypt_message($message->subject, $sharedKey);

                $message_y_json = json_encode($message_y);
                $message_json_data_for_receiver = encrypt_message($message_y_json, "");
                file_put_contents($b_path . "/" . $id_h, $message_json_data_for_receiver);

                $sent++;
            }
        }

        add_message("info", "Message sent to " . $sent . " public keys");
        return json_response(
            [
                "pair" => [
                    "from" => $sender_public_key_hash,
                    "to" => $public_keys
                ],
                "message_length" => hsize(strlen($message->content . $message->subject)),
                "id" => $id,
                "message_blake3_digest" => blake3($message->content . $message->subject)
            ]
        );
    }
}
