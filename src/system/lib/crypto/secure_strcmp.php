<?php

function secure_strcmp($h0, $h1)
{
    $h0_len = strlen($h0);
    $h1_len = strlen($h1);

    if ($h0_len != $h1_len) {
        return false;
    }

    $h0_chars = str_split($h0);
    $h1_chars = str_split($h1);

    $r = true;
    for ($i = 0; $i < strlen($h0); $i++) {
        $r &= $h0_chars[$i] == $h1_chars[$i];
    }

    return $r;
}
