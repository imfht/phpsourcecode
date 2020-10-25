<?php
$conFilePath = __DIR__ . DS . ENV . DS . 'db.php';
if (file_exists($conFilePath)){
    $db = require_once $conFilePath;
}else{
    $db = [];
}

return $db;