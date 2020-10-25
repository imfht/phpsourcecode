<?php
$conFilePath = __DIR__ . DS . ENV . DS . 'log.php';
if (file_exists($conFilePath)){
    $log = require_once $conFilePath;
}else{
    $log = [];
}

return $log;
