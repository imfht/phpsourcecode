<?php declare (strict_types = 1);
namespace msqphp\base\dir;

use msqphp\base\file\File;

/**
 * 通过文件名称删除文件
 *
 * @func_name     deleteAllFileByType
 *
 * @param  string $path   目录路径
 * @param  string $type   后缀名
 * @param  string $prefix    前缀名
 *
 * @throws DirException
 * @return void
 */
return function (string $dir, string $type, string $prefix = ''): void {
    foreach (static::getAllFileByType($dir, $type, $prefix) as $file) {
        File::delete($file, true);
    }
};
