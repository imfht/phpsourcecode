<?php
\think\Route::bind('install');
if (!is_writable(RUNTIME_PATH)) {
    header("Content-type: text/html; charset=utf-8");
    die('创建目录失败！请检查 ' . RUNTIME_PATH . ' 的目录权限！');
}
return [];