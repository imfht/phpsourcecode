<?php declare (strict_types = 1);
namespace msqphp\base\file;

/**
 * 移动文件
 * @func_name          move
 *
 * @param  string $from       目录路径
 * @param  string $to         目标路径
 *
 * @throws FileException
 * @return void
 */
return function (string $from, string $to, bool $force = true): void {
    static::copy($from, $to, $force);
    static::delete($from, $force);
};
