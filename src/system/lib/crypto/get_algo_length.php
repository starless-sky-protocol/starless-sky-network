<?php

function get_algo_length($prefix)
{
    return strlen($prefix) + BLAKE3_XOF_LENGTH * 2;
}
