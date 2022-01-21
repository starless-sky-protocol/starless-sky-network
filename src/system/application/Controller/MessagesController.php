<?php

namespace Controller;

use Inphinit\Http\Request;
use Symfony\Component\VarDumper\VarDumper;

class MessagesController
{
    public function Add()
    {
        $public_key = ($GLOBALS["request"]->public_key);
        $message = $GLOBALS["request"]->message;

        if (!is_public_key_valid($public_key)) {
            add_message("error", "Invalid public key.");
            return json_response();
        }

        $public_key_h = SLS_HASH_PREFIX . algo_gen_base34_hash($public_key);

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
            if (!json_decode($_ENV["ALLOW_NOT_IDENTIFIED_SENDERS"])) {
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

        if (strlen($message_json_data) >= $max_size = json_decode($_ENV["MESSAGE_MAX_SIZE"])) {
            add_message("error", "Message content cannot be bigger than $max_size bytes.");
            return json_response();
        }

        file_put_contents($public_key_d . "/" . SLS_HASH_PREFIX . algo_gen_base34_hash($id), $message_json_data);

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

    public function Browse()
    {
        $private_key = $GLOBALS["request"]->private_key;
        $public_key = private_key_to_public_key($private_key);
        $public_key_h = SLS_HASH_PREFIX . algo_gen_base34_hash($public_key);

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

    public function ReadFromReceiver($id)
    {
        $private_key = $GLOBALS["request"]->private_key;
        $public_key = private_key_to_public_key($private_key);
        $public_key_h = SLS_HASH_PREFIX . algo_gen_base34_hash($public_key);

        if (!is_private_key_valid($private_key)) {
            add_message("error", "Invalid private key.");
            return json_response();
        }

        if (!is_dir($public_key_d = MESSAGES_PATH . $public_key_h)) {
            add_message("warn", "This private key doesn't has any messages");
        }

        if (!is_file($file_path = $public_key_d . "/" . SLS_HASH_PREFIX . algo_gen_base34_hash($id))) {
            add_message("error", "Message not found");
            return json_response();
        }

        $message_content = file_get_contents($file_path);
        $message_decrypted = json_decode(decrypt_message($message_content, $public_key_h));
        $data[] = [
            "id" => $message_decrypted->id,
            "manifest" => $message_decrypted->manifest,
            "pair" => $message_decrypted->pair,
            "sent_to" => $public_key,
            "size" => strlen($message_content),
            "message" => [
                "subject" => $message_decrypted->subject,
                "content" => $message_decrypted->content,
            ]
        ];

        add_message("info", "Message data delivered to receiver's client");

        return json_response($data);
    }

    public function ReadFromSender($id)
    {
        $private_key = $GLOBALS["request"]->private_key;
        $public_key = $GLOBALS["request"]->public_key;
        $public_key_h = SLS_HASH_PREFIX . algo_gen_base34_hash($public_key);

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

        if (!is_file($file_path = $public_key_d . "/" . SLS_HASH_PREFIX . algo_gen_base34_hash($id))) {
            add_message("error", "Message not found");
            return json_response();
        }

        $message_content = file_get_contents($file_path);
        $message_decrypted = json_decode(decrypt_message($message_content, $public_key_h));

        if(!secure_strcmp($message_decrypted->pair->sender_public_key, private_key_to_public_key($private_key))) {
            add_message("error", "The private key does not match with the sender's private key of the message.");
            return json_response();
        }

        $data[] = [
            "id" => $message_decrypted->id,
            "manifest" => $message_decrypted->manifest,
            "pair" => $message_decrypted->pair,
            "size" => strlen($message_content),
            "message" => [
                "subject" => $message_decrypted->subject,
                "content" => $message_decrypted->content,
            ]
        ];

        add_message("info", "Message data delivered to sender's client");

        return json_response($data);
    }

    public function Edit($id)
    {
        if(json_decode($_ENV["ALLOW_MESSAGE_EDIT"]) == false) {
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

        if(secure_strcmp($message_decrypted->pair->sender_public_key, $sender_public_key = private_key_to_public_key($private_key))) {
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

    public function DeleteFromReceiver($id)
    {
        if(json_decode($_ENV["ALLOW_MESSAGE_DELETION"]) == false) {
            add_message("error", "This SLS network doens't allow deletion of messages.");
            return json_response();
        }

        $private_key = $GLOBALS["request"]->private_key;
        $public_key = private_key_to_public_key($private_key);
        $public_key_h = SLS_HASH_PREFIX . algo_gen_base34_hash($public_key);

        if (!is_private_key_valid($private_key)) {
            add_message("error", "Invalid private key.");
            return json_response();
        }

        if (!is_dir($public_key_d = MESSAGES_PATH . $public_key_h)) {
            add_message("warn", "This private key doesn't has any messages");
            return json_response();
        }
        
        if (!is_file($file_path = $public_key_d . "/" . SLS_HASH_PREFIX . algo_gen_base34_hash($id))) {
            add_message("error", "Message not found");
            return json_response();
        }

        if(unlink($file_path)) {
            add_message("info", "Message deleted");
        } else {
            add_message("error", "Message cannot be deleted");
        }

        return json_response();
    }

    public function DeleteFromSender($id)
    {
        if(json_decode($_ENV["ALLOW_MESSAGE_DELETION"]) == false) {
            add_message("error", "This SLS network doens't allow deletion of messages.");
            return json_response();
        }

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

        if(!secure_strcmp($message_decrypted->pair->sender_public_key, private_key_to_public_key($private_key))) {
            add_message("error", "The private key does not match with the sender's private key of the message.");
            return json_response();
        }

        if(unlink($file_path)) {
            add_message("info", "Message deleted");
        } else {
            add_message("error", "Message cannot be deleted");
        }

        return json_response();
    }
}
