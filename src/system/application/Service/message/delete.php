<?php

namespace svc\message;

trait delete
{
    public function delete($id)
    {
        if (config("information.allow_message_deletion") == false) {
            add_message("error", "This SLS network doens't allow deletion of messages.");
            return json_response();
        }

        $private_key = $GLOBALS["request"]->private_key;
        $public_key = algo_gen_hash($private_key, SLOPT_PRIVATE_KEY_TO_PUBLIC_KEY);
        $public_key_h = algo_gen_hash($public_key, SLOPT_PUBLIC_KEY_DIRNAME);
        $id_h = algo_gen_hash($id, SLOPT_SKYID_HASH);

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

        $file_path = $public_key_d . "/" . $id_h;
        if (!is_file($file_path)) {
            if ($dir == SENT_PATH) {
                $dir = INBOX_PATH;
                goto tryagain;
            } else {
                add_message("error", "Message not found");
                return json_response();
            }
        }

        $message_content = file_get_contents($file_path);
        $message_decrypted = json_decode(decrypt_message($message_content, algo_gen_hash($public_key, SLOPT_PUBLIC_KEY_SECRET)));

        if($dir == INBOX_PATH) { // found message on inbox folder, so he received it
            // delete for receiver
            $file = INBOX_PATH . $public_key_h . "/" . $id_h;
            unlink($file);
        } else { // found message on his sent folder, so he sent it
            // delete for sender
            $file = SENT_PATH . $public_key_h . "/" . $id_h;
            unlink($file);

            // delete for receivers
            $storeTo = $message_decrypted->pair->to;
            foreach ($storeTo as $to_public_key) {
                $to_public_key_h = algo_gen_hash($to_public_key, SLOPT_PUBLIC_KEY_DIRNAME);
                $file = INBOX_PATH . $to_public_key_h . "/" . $id_h;
                if(is_file($file) /*check if file wasn't already deleted by receiver*/) unlink($file);
            }
        }

        finish:
        add_message("info", "Message successfully deleted");
        return json_response();
    }
}
