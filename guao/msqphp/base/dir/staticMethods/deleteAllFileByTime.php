<?php declare (strict_types = 1);
namespace msqphp\base\dir;

use msqphp\base\file\File;

/**
 * 通过文件相关时间删除文件
 *
 * @func_name     deleteAllFileByTime
 *
 * @param  string $dir        目录路径
 * @param  string $type       a(fileatime.访问时间), c(filectim.文件信息改变时间), m(filectime.修改时间)
 * @param  string $expire     过期时间
 * @param  string &extension  后缀缀名
 * @param  string $prefix     前缀名
 *
 * @throws DirException
 * @return void
 */
return function (string $dir, string $type, int $expire = 3600, string $extension = '', string $prefix = ''): void {
    // 获取func 名
    switch ($type) {
        case 'a':
        case 'c':
        case 'm':
            $func = 'file' . $type . 'time';
            break;
        default:
            static::exception($type . '应为 a(fileatime.访问时间), c(filectim.文件信息改变时间), m(filectime.修改时间)');
    }

    // 过期时间
    $expire = time() - $expire;

    // 遍历获取所有文件
    foreach (static::getAllFileByType($dir, $extension, $prefix) as $file) {
        // 过期删除
        $func($file) < $expire && File::delete($file);
    }
};
