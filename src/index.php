<?php
define('INPHINIT_START', microtime(true));
define('INPHINIT_ROOT', strtr(__DIR__, '\\', '/') . '/');
define('INPHINIT_PATH', INPHINIT_ROOT . 'system/');
define('INPHINIT_COMPOSER', false);
define('SLS_VERSION', "0.12.231");

require_once INPHINIT_PATH . 'boot/start.php';
