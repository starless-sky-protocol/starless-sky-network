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

        $private_key_raw = $GLOBALS["request"]->private_key;
        $private_key = load($private_key_raw);
        $public_key = $private_key->getPublicKey();
        $public_key_h = algo_gen_hash($public_key->toString("PKCS8"), SLOPT_PUBLIC_KEY_ADDRESS);
        $public_key_d = algo_gen_hash($public_key_h, SLOPT_PUBLIC_KEY_DIRNAME);
        $id_h = algo_gen_hash($id, SLOPT_SKYID_HASH);

        $dir = SENT_PATH;
        tryagain:
        if (!is_dir($fullpath = $dir . $public_key_d)) {
            if ($dir == SENT_PATH) {
                $dir = INBOX_PATH;
                goto tryagain;
            } else {
                add_message("warn", "This private key doesn't has any messages");
            }
        }

        $file_path = $fullpath . "/" . $id_h;
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
        $message_decrypted = json_decode(decrypt_message($message_content, ""));

        if ($dir == INBOX_PATH) { // found message on inbox folder, so he received it
            // delete for receiver
            $file = INBOX_PATH . $public_key_d . "/" . $id_h;
            unlink($file);
        } else { // found message on his sent folder, so he sent it
            // delete for sender
            $file = SENT_PATH . $public_key_d . "/" . $id_h;
            unlink($file);

            // delete for receivers
            $storeTo = $message_decrypted->pair->to;
            foreach ($storeTo as $to_public_key) {
                $to_public_key_h = algo_gen_hash($to_public_key, SLOPT_PUBLIC_KEY_DIRNAME);
                $file = INBOX_PATH . $to_public_key_h . "/" . $id_h;
                if (is_file($file) /*check if file wasn't already deleted by receiver*/) unlink($file);
            }
        }

        finish:
        add_message("info", "Message successfully deleted");
        return json_response();
    }
}
