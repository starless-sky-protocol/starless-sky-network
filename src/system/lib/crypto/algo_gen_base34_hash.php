<?php

function algo_gen_base34_hash($content)
{
    return "0x" . bin2hex(hmac_blake3($content, $_ENV["BASE_HMAC_KEY"], true));
}
