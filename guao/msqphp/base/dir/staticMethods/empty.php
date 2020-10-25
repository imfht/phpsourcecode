<?php declare (strict_types = 1);
namespace msqphp\base\dir;

use msqphp\base\file\File;

/**
 * 清空目录内容
 *
 * @func_name               empty
 *
 * @param  string $dir      目录路径
 * @param  bool   $force    为空创建
 *
 * @throws DirException
 * @return void
 */
return function (string $dir, bool $force = true): void {
    // 目录检测
    if (!is_dir($dir)) {
        if ($force) {
            static::make($dir, $force);
        } else {
            static::exception($dir . '目录不存在, 无法清空');
        }
    } else {
        // 权限判断
        is_writable($dir) || static::exception($dir . '目录不可操作, 无法清空');
        // 清空目录
        foreach (static::getDirList($dir, true) as $children_dir) {
            static::delete($children_dir, true);
        }
        foreach (static::getFileList($dir, true) as $children_file) {
            File::delete($children_file, true);
        }
    }
};
