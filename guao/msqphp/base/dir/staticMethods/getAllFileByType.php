<?php declare (strict_types = 1);
namespace msqphp\base\dir;

/**
 * 根据类型获取指定目录下所有对应文件
 *
 * @func_name     getAllFileByType
 *
 * @param  string $dir 目录
 * @param  string $type 类型
 * @param  string $type 前缀
 *
 * @throws DirException
 * @return 一维索引数组，值为文件绝对路径
 */
return function (string $dir, string $type = '*', string $pre = ''): array{
    // 无法操作
    is_dir($dir) || static::exception($dir . '文件夹不存在');

    is_readable($dir) || static::exception($dir . '文件夹不可读');

    $dir = realpath($dir) . DIRECTORY_SEPARATOR;

    false === strpos($type, '.') && $type = '.' . $type;

    $files = glob($dir . $pre . '*' . $type);

    foreach (static::getDirList($dir, true) as $children_dir) {
        $files = array_merge($files, static::getAllFileByType($children_dir, $type, $pre));
    }

    return $files;
};
