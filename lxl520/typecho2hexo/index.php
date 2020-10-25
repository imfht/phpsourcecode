<?php
require_once './vendor/autoload.php';
try {
    new \Mohuishou\Lib\Convert();
}catch (Exception $e){
    echo "\r\n error： ".$e->getMessage();
}

