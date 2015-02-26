<?php

if(file_exists(dirname(__DIR__).'/vendor/autoload.php')) {
    require_once( dirname(__DIR__).'/vendor/autoload.php' );
    return;
}
elseif(file_exists(dirname(dirname(dirname(__DIR__))).'/autoload.php')) {
    require_once( dirname(dirname(dirname(__DIR__))).'/autoload.php' );
    return;
}
exit;
