<?php

define('STORAGE_PATH', INPHINIT_ROOT . 'storage/');
define("INBOX_PATH", STORAGE_PATH . "messages-from/");
define("SENT_PATH", STORAGE_PATH . "messages-to/");
define("IDENTITY_PATH", STORAGE_PATH . "identity/");
define("CONTRACT_FROM_PATH", STORAGE_PATH . "sign-from/");
define("CONTRACT_TO_PATH", STORAGE_PATH . "sign-to/");
define("TRANSACTIONS_PATH", STORAGE_PATH . "transactions/");

if (!is_dir(STORAGE_PATH)) {
    mkdir(STORAGE_PATH, 0777) or die("Cannot create storage directory");
}
if (!is_dir(INBOX_PATH)) {
    mkdir(INBOX_PATH, 0777) or die("Cannot create messages data directory");
}
if (!is_dir(IDENTITY_PATH)) {
    mkdir(IDENTITY_PATH, 0777) or die("Cannot create identity data directory");
}
if (!is_dir(CONTRACT_FROM_PATH)) {
    mkdir(CONTRACT_FROM_PATH, 0777) or die("Cannot create signing data directory");
}
if (!is_dir(CONTRACT_TO_PATH)) {
    mkdir(CONTRACT_TO_PATH, 0777) or die("Cannot create signing data directory");
}
if (!is_dir(SENT_PATH)) {
    mkdir(SENT_PATH, 0777) or die("Cannot create sent data directory");
}
if (!is_dir(TRANSACTIONS_PATH)) {
    mkdir(TRANSACTIONS_PATH, 0777) or die("Cannot create transactions data directory");
}