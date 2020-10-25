<?php declare(strict_types = 1);
namespace msqphp\base\dir;

/**
 * 移动目录, 文件
 *
 * @func_name                 move
 *
 * @param  string $from       目录路径
 * @param  string $to         目标路径
 * @param  string $force      不存在则创建, 存在则替换
 *
 * @throws DirException
 * @return void
 */
return function (string $from, string $to, bool $force = true) : void {
    static::copy($from, $to, $force);
    static::delete($from, $force);
};