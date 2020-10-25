<?php
$conFilePath = __DIR__ . DS . ENV . DS . 'session.php';
if (file_exists($conFilePath)){
    $session = require_once $conFilePath;
}else{
    $session = [];
}

return $session;