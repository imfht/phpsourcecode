<?php declare (strict_types = 1);
namespace msqphp\base\file;

/**
 * 复制文件
 *
 * @func_name     copy
 *
 * @param  string $from       文件路径
 * @param  string $to         文件路径
 * @param  string $force      不存在则创建, 存在则替换
 *
 * @throws FileException
 * @return void
 */

return function (string $from, string $to, bool $force = true): void{
    // 是否存在
    is_file($from) || static::exception($from . '源文件不存在, 无法复制');

    // 是否可操作
    is_readable($from) || static::exception($from . '源文件不可读,无法复制');

    // 对应文件是否存在
    if (is_file($to)) {
        if ($force) {
            static::delete($to, true);
        } else {
            static::exception($to . '目标文件已存在无法复制');
        }
    }

    // 对应文件父目录是否可操作
    is_writable(dirname($to)) || static::exception($to . '目标文件父目录不可写,无法复制');

    // 复制
    copy($from, $to) || static::exception('未知错误,无法复制.[源文件]:' . $from . '[目标文件]:' . $to);
};
