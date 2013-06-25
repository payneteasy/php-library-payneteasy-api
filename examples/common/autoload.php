<?php

spl_autoload_register(function($class)
{
    if(strpos($class, 'PaynetEasy\Paynet') === 0)
    {
        $class = substr($class, strlen('PaynetEasy\Paynet') + 1);
    }

    $file = __DIR__ . '/source/'.str_replace('\\', '/', $class).'.php';

    if (is_file($file))
    {
        require_once $file;
    }
});
