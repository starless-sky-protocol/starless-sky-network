<?php

foreach(rsearch(INPHINIT_PATH . "application/Service", ["php"]) as $file) {
    require_once $file;
}