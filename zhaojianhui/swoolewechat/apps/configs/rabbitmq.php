<?php
$conFilePath = __DIR__ . DS . ENV . DS . 'rabbitmq.php';
if (file_exists($conFilePath)){
    $rabbitmq = require_once $conFilePath;
}else{
    $rabbitmq = [];
}

return $rabbitmq;