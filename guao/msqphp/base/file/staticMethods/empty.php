<?php declare (strict_types = 1);
namespace msqphp\base\file;

/**
 * 清空文件内容
 *
 * @func_name     empty
 *
 * @param  string $file     文件路径
 * @param  bool   $force    为空创建
 *
 * @throws FileException
 * @return void
 */

return function (string $file, bool $force = true): void {
    // 文件不存在
    if (!is_file($file)) {
        if ($force) {
            static::write($file, '', true);
        } else {
            static::exception($file . '文件不存在, 无法清空内容');
        }
    } else {
        // 创建一个空文件
        static::write($file, '', true);
    }
};
