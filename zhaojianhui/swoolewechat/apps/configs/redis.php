<?php
$conFilePath = __DIR__ . DS . ENV . DS . 'redis.php';
if (file_exists($conFilePath)){
    $redis = require_once $conFilePath;
}else{
    $redis = [];
}

return $redis;