<?php

namespace svc\message;

trait read
{
    public function read($id)
    {
        $private_key = $GLOBALS["request"]->private_key;
        $public_key = algo_gen_hash($private_key, SLOPT_PRIVATE_KEY_TO_PUBLIC_KEY);
        $public_key_h = algo_gen_hash($public_key, SLOPT_PUBLIC_KEY_DIRNAME);

        if (!is_hash_valid($private_key)) {
            add_message("error", "Invalid private key.");
            return json_response();
        }

        $dir = SENT_PATH;
        tryagain:
        if (!is_dir($public_key_d = $dir . $public_key_h)) {
            if ($dir == SENT_PATH) {
                $dir = INBOX_PATH;
                goto tryagain;
            } else {
                add_message("warn", "This private key doesn't has any messages");
            }
        }

        $file_path = $public_key_d . "/" . algo_gen_hash($id, SLOPT_SKYID_HASH);
        if (!is_file($file_path)) {
            if ($dir == SENT_PATH) {
                $dir = INBOX_PATH;
                goto tryagain;
            } else {
                add_message("error", "Message not found");
                return json_response();
            }
        }

        $key = algo_gen_hash($public_key, SLOPT_PUBLIC_KEY_SECRET);
        $message_content = file_get_contents($file_path);
        $message_decrypted = json_decode(decrypt_message($message_content, $key));
        $message_decrypted->read = true;
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

        file_put_contents($file_path, encrypt_message(json_encode($message_decrypted), $key));

        return json_response($data);
    }
}
