<?php

foreach(rsearch(INPHINIT_PATH . "lib", ["php"]) as $file) {
    if(basename($file) == "__driver.php") {
        continue;
    }
    require_once $file;
}