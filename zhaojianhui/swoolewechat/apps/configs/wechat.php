<?php
$conFilePath = __DIR__ . DS . ENV . DS . 'wechat.php';
if (file_exists($conFilePath)){
    $wechat = require_once $conFilePath;
}else{
    $wechat = [];
}

return $wechat;