<?php declare (strict_types = 1);
namespace msqphp\base\file;

/**
 * 获取文件mime
 *
 * @func_name  mime
 *
 * @param   string $file 文件名
 * @return  string
 */
return function (string $file): string{

    // 文件是否存在
    is_file($file) || static::exception($file . '文件不存在');

    // 扩展是否存在
    function_exists('finfo_open') || static::exception('需要php_fileinfo扩展');

    $finfo = finfo_open(FILEINFO_MIME);
    $mime  = finfo_file($finfo, $filename);
    finfo_close($finfo);

    return $mime;
};
