<?php

define('STORAGE_PATH', INPHINIT_ROOT . 'storage/');
define("MESSAGES_PATH", STORAGE_PATH . "messages/");

if(!is_dir(MESSAGES_PATH)) {
    mkdir(MESSAGES_PATH, 0777) or die("Cannot create messages data directory");
}