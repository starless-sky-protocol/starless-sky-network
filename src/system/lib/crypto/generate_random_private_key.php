<?php

use phpseclib3\Crypt\DH;

function generante_random_private_key()
{
    $params = DH::createParameters("diffie-hellman-group15-sha512");
    $k = DH::createKey($params);

    return $k;
}
