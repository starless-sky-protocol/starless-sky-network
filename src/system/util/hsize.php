<?php

/*
    Project Starless Sky Protocol
    Copyright 2022 Project Principium and Starless Sky authors

    This project is distributed under the MIT License, that is, you can modify,
    publish or sell this file, as long as you have brief mention of the project
    and the code snippet used (if not all).

    The project is distributed under no warranty, that is, there is no
    responsibility for consequences or contents circulating in the
    networks created by this project.

    Editing this file will directly interfere with the functioning of your
    network. Unless you know what you're doing, read the documentation.
    If you think this edit is interesting for the project, submit a commit in the
    project official repository:

    https://github.com/starless-sky-protocol/starless-sky-network
*/

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
    if ($size >= 1 << 30) return floor($size / (1 << 30)) . " Gb";
    if ($size >= 1 << 20) return floor($size / (1 << 20)) . " Mb";
    if ($size >= 1 << 10) return floor($size / (1 << 10)) . " Kb";
    return floor($size) . " bytes";
}
