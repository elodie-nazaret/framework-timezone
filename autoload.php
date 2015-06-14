<?php

function __autoload($className) {
    $class = substr($className, strpos($className, '\\') + 1);
    require __DIR__ . '\\' . $class . '.php';
}