<?php

function __autoload($className) {
    $class = '';
    if (strtolower(substr($className, 0, 9)) != 'framework') {
        $class = '..\\';
    }

    $class .= substr($className, strpos($className, '\\') + 1);

    require __DIR__ . '\\' . $class . '.php';
}