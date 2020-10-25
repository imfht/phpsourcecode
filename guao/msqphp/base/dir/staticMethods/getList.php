<?php declare(strict_types = 1);
namespace msqphp\base\dir;

/**
 * 得到当前目录列表
 *
 * @func_name       getList
 *
 * @param  string   $dir  路径
 * @param  string   $type 获取类型
 * @param  bool     $type 是否返回绝对路径
 *
 * @throws DirException
 * @return array
 */
return function (string $dir, string $type = 'all', bool $absolute = false) : array {
    switch ($type) {
        case 'all' :
            return array_merge(static::getDirlist($dir, $absolute), static::getFilelist($dir, $absolute));
        case 'file':
            return static::getFilelist($dir, $absolute);
        case 'dir' :
            return static::getDirlist($dir, $absolute);
        default:
            static::exception($type . '为未知的类型,应为all|file|dir');
    }
};