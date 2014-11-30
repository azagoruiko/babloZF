<?php
function loadCommon($class) {
    $path = '../common/' . str_replace('\\', '/', $class . '.php');
    if (file_exists($path))        require_once $path;
}

function loadProject($class) {
    $path = '../module/Bablo/src/' . str_replace('\\', '/', $class . '.php');
    //echo $path;
    if (file_exists($path))        require_once $path;
}

function loadMocks($class) {
    $path = 'module/Bablo/src/' . str_replace('\\', '/', $class . '.php');
    if (file_exists($path))        require_once $path;
}

function loadZend($class) {
    $path = '../vendor/zendframework/zendframework/library/' . str_replace('\\', '/', $class . '.php');
    if (file_exists($path))        require_once $path;
}

spl_autoload_register("loadCommon");
spl_autoload_register("loadProject");
spl_autoload_register("loadZend");
spl_autoload_register("loadMocks");