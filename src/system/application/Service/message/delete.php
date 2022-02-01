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

        if (!is_private_key_valid($private_key)) {
            add_message("error", "Invalid private key.");
            return json_response();
        }

        $dir = SENT_PATH;
    tryagain:
        if (!is_dir($public_key_d = $dir . $public_key_h)) {
            if($dir == SENT_PATH) {
                $dir = INBOX_PATH;
                goto tryagain;
            } else {
                add_message("warn", "This private key doesn't has any messages");
            }
        }

        if (!is_file($file_path = $public_key_d . "/" . $id_h)) {
            add_message("error", "Message not found");
            return json_response();
        }

        $message_content = file_get_contents($file_path);
        $message_decrypted = json_decode(decrypt_message($message_content, algo_gen_hash($public_key, SLOPT_PUBLIC_KEY_KEY)));
        if ($dir == SENT_PATH) {
            if ($message_decrypted->pair->from != $public_key) {
                add_message("error", "Insufficient permissions to delete this message. You do not own it.");
                return json_response();
            }
        } else {
            if ($message_decrypted->pair->to != $public_key) {
                add_message("error", "Insufficient permissions to delete this message. You do not own it.");
                return json_response();
            }
        }

        $fromPublicKeyDirname = algo_gen_hash($message_decrypted->pair->from, SLOPT_PUBLIC_KEY_DIRNAME);
        $toPublicKeyDirname = algo_gen_hash($message_decrypted->pair->to, SLOPT_PUBLIC_KEY_DIRNAME);

        $a = true;
        if($message_decrypted->pair->from != null) $a = unlink(SENT_PATH . $fromPublicKeyDirname . "/" . $id_h);
        
        $b = unlink(INBOX_PATH . $toPublicKeyDirname . "/" . $id_h);

        if ($a && $b) {
            add_message("info", "Message successfully deleted");
        } else {
            add_message("error", "Cannot delete message");
        }

        return json_response();
    }
}
