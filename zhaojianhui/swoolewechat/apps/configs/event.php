<?php
$conFilePath = __DIR__ . DS . ENV . DS . 'event.php';
if (file_exists($conFilePath)){
    $event = require_once $conFilePath;
}else{
    $event = [];
}

return $event;
