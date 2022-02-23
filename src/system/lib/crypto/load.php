<?php

use phpseclib3\Crypt\DH;

function load(string $rawKey)
{
    return DH::load($rawKey);
}

function load_from_public_hash(string $publicKeyHex)
{
    return DH::load(decrypt_message(file_get_contents(PUBLIC_KEY_DERIVES_PATH . $publicKeyHex), SLOPT_PUBLIC_KEY_SECRET["salt"]));
}
