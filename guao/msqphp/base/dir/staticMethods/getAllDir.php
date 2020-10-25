<?php declare (strict_types = 1);
namespace msqphp\base\dir;

/**
 * 根据类型获取指定目录下所有对应文件
 *
 * @func_name     getAllDir
 *
 * @param  string $dir 目录
 *
 * @throws DirException
 * @return 一维索引数组，值为文件绝对路径
 */
return function (string $dir): array{

    is_readable($dir) || static::exception($dir . ' 文件夹不存在或无法操作');

    $dir_list = static::getDirList($dir, true);

    foreach ($dir_list as $children_dir) {
        $dir_list = array_merge($dir_list, static::getAllDir($children_dir));
    }

    return $dir_list;
};
