<?php declare (strict_types = 1);
namespace msqphp\base\file;

use msqphp\base\number\Number;

/**
 * 得到文件大小
 *
 * @func_name     getFileSize
 *
 * @param   string $file 路径
 * @param   bool    $round      是否保留整数
 * @param   bool    $unit       是否带单位
 *
 * @rely on msqphp\base\number\Number::byte();
 *
 * @throws  FileException
 * @return  strging|int
 */

return function (string $file, bool $round = true, bool $unit = true) {

    is_file($file) || static::exception($file . ' 文件不存在');

    // 获取字节大小
    $size = filesize($file);

    $round && $size = round($size);

    $unit && $size = Number::byte($size);

    return $size;
};
