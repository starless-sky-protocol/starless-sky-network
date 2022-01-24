<?php

namespace svc\message;

trait delete_from_receiver
{
    public function delete_from_receiver($id)
    {
        if (json_decode($_ENV["ALLOW_MESSAGE_DELETION"]) == false) {
            add_message("error", "This SLS network doens't allow deletion of messages.");
            return json_response();
        }

        $private_key = $GLOBALS["request"]->private_key;
        $public_key = private_key_to_public_key($private_key);
        $public_key_h = algo_gen_base34_hash($public_key);

        if (!is_private_key_valid($private_key)) {
            add_message("error", "Invalid private key.");
            return json_response();
        }

        if (!is_dir($public_key_d = MESSAGES_PATH . $public_key_h)) {
            add_message("warn", "This private key doesn't has any messages");
            return json_response();
        }

        if (!is_file($file_path = $public_key_d . "/" . algo_gen_base34_hash($id))) {
            add_message("error", "Message not found");
            return json_response();
        }

        if (unlink($file_path)) {
            add_message("info", "Message deleted");
        } else {
            add_message("error", "Message cannot be deleted");
        }

        return json_response();
    }
}
