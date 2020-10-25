<?php declare (strict_types = 1);
namespace msqphp\base\dir;

use msqphp\base\file\File;

/**
 * 复制目录
 * @param  string $from       目录路径
 * @param  string $to         目标路径
 * @param  string $force      不存在则创建, 存在则替换
 * @throws DirException
 * @return void
 */
return function (string $from, string $to, bool $force = false): void{
    // 原目录是否存在
    is_dir($from) || static::exception($from . ' 目录不存在');

    // 是否可操作
    is_readable($from) || static::exception($from . ' 无法操作,无法复制');

    // 目标目录是否存在
    if (is_dir($to)) {
        if ($force) {
            static::empty($to, true);
        } else {
            static::exception($to . ' 目录已存在');
        }
    } else {
        static::make($to);
    }

    $to_parent = dirname($to);

    // 目标父目录是否可操作
    is_writable($to_parent) || static::exception($to . ' 父目录无法操作');

    $from = realpath($from) . DIRECTORY_SEPARATOR;
    $to   = realpath($to) . DIRECTORY_SEPARATOR;

    // 复制目录
    foreach (static::getDirList($from, false) as $dir) {
        static::copy($from . $dir, $to . $dir, true);
    }

    // 复制文件
    foreach (static::getFileList($from, false) as $file) {
        File::copy($from . $file, $to . $file, true);
    }
};
