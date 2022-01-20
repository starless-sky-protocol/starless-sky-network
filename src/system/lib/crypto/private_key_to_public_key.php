<?php

function private_key_to_public_key($private_key_content)
{
    $output = algo_gen_base34_hash($private_key_content);
    return SLS_PUBLIC_KEY_PREFIX . $output;
}
