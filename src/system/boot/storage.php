<?php

define('STORAGE_PATH', INPHINIT_ROOT . 'storage/');
define("MESSAGES_PATH", STORAGE_PATH . "messages/");
define("IDENTITY_PATH", STORAGE_PATH . "identity/");
define("SIGNING_PATH", STORAGE_PATH . "sign/");

if(!is_dir(STORAGE_PATH)) {
    mkdir(STORAGE_PATH, 0777) or die("Cannot create storage directory");
}
if(!is_dir(MESSAGES_PATH)) {
    mkdir(MESSAGES_PATH, 0777) or die("Cannot create messages data directory");
}
if(!is_dir(IDENTITY_PATH)) {
    mkdir(IDENTITY_PATH, 0777) or die("Cannot create identity data directory");
}
if(!is_dir(SIGNING_PATH)) {
    mkdir(SIGNING_PATH, 0777) or die("Cannot create signing data directory");
}