<?php

use phpseclib3\Crypt\DH;

function shared_key($private_key, $public_key)
{
    return base64_encode(DH::computeSecret($private_key, $public_key));
}
