<?php

namespace svc\message;

trait browse
{
    public function browse()
    {
        $private_key = $GLOBALS["request"]->private_key;
        $public_key = private_key_to_public_key($private_key);
        $public_key_h = algo_gen_base34_hash($public_key);

        if (!is_private_key_valid($private_key)) {
            add_message("error", "Invalid private key.");
            return json_response();
        }

        if (!is_dir($private_key_d = MESSAGES_PATH . $public_key_h)) {
            add_message("warn", "This private key doesn't has any messages");
            return json_response();
        }

        $pagination_data = $GLOBALS["request"]->pagination_data;
        $glob = glob($private_key_d . "/*");
        $data = [];

        if (count($glob) < $pagination_data->skip) {
            add_message("warn", "Pagination skip is greater than total messages on this private key");
        }
        if ($pagination_data->take == 0) {
            add_message("warn", "Pagination take is zero");
        }
        if ($pagination_data->take == -1) {
            add_message("warn", "Pagination take is infinite. All stored data is being returned.");
        }

        foreach (array_slice($glob, $pagination_data->skip, $pagination_data->take == -1 ? count($glob) : $pagination_data->take) as $message) {
            $message_content = file_get_contents($message);
            $message_decrypted = json_decode(decrypt_message($message_content, $public_key_h));
            $data[] = [
                "id" => $message_decrypted->id,
                "created_at" => $message_decrypted->manifest->created_at,
                "is_modified" => $message_decrypted->manifest->is_modified,
                "message_blake3_digest" => $message_decrypted->manifest->message_blake3_digest,
                "message" => [
                    "subject" => substr($message_decrypted->subject, 0, 32),
                    "content" => substr($message_decrypted->content, 0, 32),
                ]
            ];
        }

        add_message("info", "Query performed successfully");

        return json_response(
            [
                "pagination_data" => [
                    "total" => count($glob),
                    "query" => count($data)
                ],
                "messages" => array_reverse($data)
            ]
        );
    }
}

