<?php

use Inphinit\App;

require_once INPHINIT_PATH . 'vendor/inphinit/framework/src/Utils.php';

if (INPHINIT_COMPOSER) {
    require_once INPHINIT_PATH . 'vendor/autoload.php';
} else {
    UtilsAutoload();
}

UtilsConfig();

if (App::env('development')) {
    require_once INPHINIT_PATH . 'dev.php';
}

require_once INPHINIT_PATH . 'main.php';
require_once INPHINIT_PATH . 'boot/storage.php';
require_once INPHINIT_PATH . 'boot/openssltests.php';

// Require util classes
foreach(glob(INPHINIT_PATH . "util/*.php") as $file){
    require_once $file;
}

require_once INPHINIT_PATH . 'lib/__driver.php';
require_once INPHINIT_PATH . 'boot/errorhandler.php';
require_once INPHINIT_PATH . 'boot/services.php';
require_once INPHINIT_PATH . 'boot/cors.php';

App::exec();
