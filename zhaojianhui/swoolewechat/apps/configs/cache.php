<?php
$conFilePath = __DIR__ . DS . ENV . DS . 'cache.php';
if (file_exists($conFilePath)){
    $cache = require_once $conFilePath;
}else{
    $cache = [];
}

return $cache;
