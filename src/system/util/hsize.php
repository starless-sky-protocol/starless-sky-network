<?php

function parse_hsize(string $hsize): int
{
    $lchar = strtoupper(substr($hsize, -1));
    $size = preg_replace('/[^0-9]/', '', $hsize);
    switch ($lchar) {
        case "B":
            return $size;
        case "K":
            return $size * pow(1024, 1);
        case "M":
            return $size * pow(1024, 2);
        case "G":
            return $size * pow(1024, 3);
        case "T":
            return $size * pow(1024, 4);
    }
}

function hsize($size)
{
    if ($size >= 1 << 30) return floor($size / (1 << 30), 2) . " Gb";
    if ($size >= 1 << 20) return floor($size / (1 << 20), 2) . " Mb";
    if ($size >= 1 << 10) return floor($size / (1 << 10), 2) . " Kb";
    return floor($size) . " bytes";
}
