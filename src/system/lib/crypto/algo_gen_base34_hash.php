<?php

function algo_gen_base34_hash($content)
{
    $h = array_values(unpack("C*", hmac_blake3($content, $_ENV["BASE_HMAC_KEY"], true)));
    $b = "";
    for ($i = 0; $i < count($h); $i++) {
        $b .= str_pad(base_convert($h[$i], 10, 34), 2, "z");
    }
    return $b;
}
