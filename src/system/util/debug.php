<?php

function dd(...$expression) {
    echo "<style>pre {background-color: whitesmoke; border: 1px solid gainsboro; padding: 5px; font-family: monospace}</style>";

    foreach($expression as $exp) {
        echo "<pre>";
        var_dump($exp);
        echo "</pre>";
    }
    die();
}