<?php declare (strict_types = 1);
namespace msqphp\base\dir;

/**
 * 得到当前目录列表
 *
 * @func_name       getFileList
 *
 * @param  string   $dir  路径
 * @param  bool     $type 是否返回绝对路径
 *
 * @throws DirException
 * @return array
 */
return function (string $dir, bool $absolute = false): array{

    // 无法操作
    is_dir($dir) || static::exception($dir . '文件夹不存在');

    is_readable($dir) || static::exception($dir . '文件夹不可读');

    $dir = realpath($dir) . DIRECTORY_SEPARATOR;

    $list = scandir($dir, 0);

    if ($absolute) {
        $result = [];
        foreach ($list as $path) {
            is_file($dir . $path) && $result[] = $dir . $path;
        }
        return $result;
    } else {
        return array_filter($list, function (string $path) use ($dir) {
            return is_file($dir . $path);
        }, 0);
    }
};
