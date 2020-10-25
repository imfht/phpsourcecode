<?php declare (strict_types = 1);
namespace msqphp\base\dir;

/**
 * 目录是否为空
 *
 * @func_name   isEmpty
 *
 * @param  string $dir      目录路径
 *
 * @throws DirException
 * @return bool
 */
return function (string $dir): bool{
    // 不存在
    is_dir($dir) || static::exception($dir . ' 不存在');

    // 不可读
    is_readable($dir) || static::exception($dir . '不可读');

    // scandir 获取当前目录列表, 如果为空, 则只有 . 和 ..
    return count(scandir($dir)) === 2;
};
