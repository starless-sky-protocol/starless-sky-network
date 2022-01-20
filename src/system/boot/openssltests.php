<?php

openssl_random_pseudo_bytes(2, $strong_result_test);

if ($strong_result_test == false) {
    die("Cannot use StarlessSky Network in this operating system. Strong Cryptography algorythms are necessary.");
}