<?php

namespace svc\signing;

trait browse
{
    public function browse()
    {
        $folder = $GLOBALS["request"]->folder;
        $private_key = $GLOBALS["request"]->private_key;
        $public_key = algo_gen_hash($private_key, SLOPT_PRIVATE_KEY_TO_PUBLIC_KEY);
        $public_key_h = algo_gen_hash($public_key, SLOPT_PUBLIC_KEY_DIRNAME);

        if (!is_hash_valid($private_key)) {
            add_message("error", "Invalid private key.");
            return json_response();
        }

        switch (strtolower($folder)) {
            case "inbox":
                $dir = CONTRACT_FROM_PATH;
                break;
            case "sent":
                $dir = CONTRACT_TO_PATH;
                break;
            default:
                add_message("error", "Invalid folder. It must be 'inbox' or 'sent'.");
                return json_response();
        }

        tryagain:
        if (!is_dir($directory = $dir . $public_key_h)) {
            add_message("warn", "Your query did not return any information.");
            return json_response(
                [
                    "pagination_data" => [
                        "total" => 0,
                        "query" => 0
                    ],
                    "contracts" => []
                ]
            );
        }

        $pagination_data = $GLOBALS["request"]->pagination_data;
        $glob = glob($directory . "/*");
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
            $contract_decrypted = json_decode(decrypt_message($message_content, algo_gen_hash($public_key, SLOPT_PUBLIC_KEY_SECRET)));
            $data[] = [
                "id" => $contract_decrypted->id,
                "issued" => $contract_decrypted->issued,
                "from" => $contract_decrypted->issuer->public_key,
                "to" => $contract_decrypted->signer->public_key,
                "message" => $contract_decrypted->message,
                "sign_status" => $contract_decrypted->status->sign_status
            ];
        }

        usort($data, function ($a, $b) {
            return $a["issued"] <=> $b["issued"];
        });

        add_message("info", "Query performed successfully");

        return json_response(
            [
                "pagination_data" => [
                    "total" => count($glob),
                    "query" => count($data)
                ],
                "contracts" => array_reverse($data)
            ]
        );
    }
}
