<?php declare(strict_types = 1);
namespace msqphp\base\dir;

use msqphp\base\file\File;
use msqphp\base\number\Number;

/**
 * 得到目录文件大小
 *
 * @func_name       getSize
 *
 * @param   string  $path 路径
 * @param   bool    $round      是否保留整数
 * @param   bool    $unit       是否带单位
 *
 * @rely on msqphp\base\number\Number::byte();
 *
 * @throws  DirException
 * @return  strging|int
 */
return function (string $dir, bool $round = true, bool $unit = true) {

    is_dir($dir) || static::exception($dir.' 文件夹不存在');

    is_readable($dir) || static::exception($dir.' 文件夹不可读');

    $size = 0;

    foreach (static::getDirList($dir, true) as $children_dir) {
        $size += static::getSize($children_dir, false, false);
    }

    foreach (static::getFileList($dir, true) as $children_file) {
        $size += File::getSize($children_file, false, false);
    }

    $round && $size = round($size);

    $unit  && $size = Number::byte($size);

    return $size;
};