<?php

use Inphinit\Config;

function config($key, $default_value = null) {
    $config = Config::load('config');
    return $config->get($key, $default_value);
}