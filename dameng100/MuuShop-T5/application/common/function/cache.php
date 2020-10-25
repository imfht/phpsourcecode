<?php

/**
 * 清理全部缓存
 */
function clean_all_cache()
{
    $dirname = './Runtime/';

//清文件缓存
    $dirs = array($dirname);
//清理缓存
    foreach ($dirs as $value) {
        rmdirr($value);
    }
    @mkdir($dirname, 0777, true);
}


function rmdirr($dirname)
{
    if (!file_exists($dirname)) {
        return false;
    }
    if (is_file($dirname) || is_link($dirname)) {
        return unlink($dirname);
    }
    $dir = dir($dirname);
    if ($dir) {
        while (false !== $entry = $dir->read()) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
        }
    }
    $dir->close();
    return rmdir($dirname);
}

/**
 * 清理指定位置缓存
 * @param $dirname
 * @author 郑钟良<zzl@ourstu.com>
 */
function clean_cache($dirname='./Runtime/')
{
    //清文件缓存
    $dirs = array($dirname);
    //清理缓存
    foreach ($dirs as $value) {
        rmdirr($value);
    }
    @mkdir($dirname, 0777, true);
}


