<?php
$conFilePath = __DIR__ . DS . ENV . DS . 'mongo.php';
if (file_exists($conFilePath)){
    $mongo = require_once $conFilePath;
}else{
    $mongo = [];
}

return $mongo;