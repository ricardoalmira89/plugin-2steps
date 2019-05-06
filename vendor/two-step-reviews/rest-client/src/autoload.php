<?php

//require_once __DIR__.'/../vendor/autoload.php';

//--- Cargar los files dentro de Resources
$files  = scandir(__DIR__.'/Resource');
$exclude = ['.', '..'];
foreach ($files as $file){

    if (!in_array($file, $exclude)){
        $f = __DIR__.DIRECTORY_SEPARATOR.'Resource'.DIRECTORY_SEPARATOR.$file;
        require_once $f;
    }

}

require_once  __DIR__.DIRECTORY_SEPARATOR.'Client.php';
require_once  __DIR__.DIRECTORY_SEPARATOR.'AuthManager.php';