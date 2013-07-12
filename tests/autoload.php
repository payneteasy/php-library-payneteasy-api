<?php
define('UNIT_TEST_ROOT', dirname(__FILE__));

set_include_path
(
    dirname(__FILE__).PATH_SEPARATOR.
    dirname(__DIR__).'/tests/source/'.PATH_SEPARATOR.
    dirname(__DIR__).'/tests'.PATH_SEPARATOR.
    dirname(__DIR__).'/source'.PATH_SEPARATOR.
    get_include_path()
);

spl_autoload_register(function($class)
{
    $class = '/'.str_replace('\\', '/', $class).'.php';

    foreach(explode(PATH_SEPARATOR, get_include_path()) as $path)
    {
        if(is_file($path.$class))
        {
            include_once $path.$class;
            return;
        }
    }
});
