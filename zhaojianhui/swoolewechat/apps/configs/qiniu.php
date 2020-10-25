<?php
$conFilePath = __DIR__ . DS . ENV . DS . 'qiniu.php';
if (file_exists($conFilePath)){
    $qiniu = require_once $conFilePath;
}else{
    $qiniu = [];
}

return $qiniu;