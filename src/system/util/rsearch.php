<?php

// https://stackoverflow.com/questions/17160696/php-glob-scan-in-subfolders-for-a-file
function rsearch($folder, $pattern_array) {
    $return = array();
    $iti = new RecursiveDirectoryIterator($folder);
    foreach(new RecursiveIteratorIterator($iti) as $file){
        if(in_array(pathinfo($file)["extension"], $pattern_array)) {
            $return[] = $file;
        }
    }
    return $return;
}