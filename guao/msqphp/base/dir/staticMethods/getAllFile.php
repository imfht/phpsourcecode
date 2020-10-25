<?php declare (strict_types = 1);
namespace msqphp\base\dir;

/**
 * 根据类型获取指定目录下所有对应文件
 *
 * @func_name     getAllFile
 *
 * @param  string $dir 目录
 *
 * @throws DirException
 * @return 一维索引数组，值为文件绝对路径
 */
return function (string $dir): array{

    is_readable($dir) || static::exception($dir . ' 文件夹不存在或无法操作');

    $file_list = static::getFileList($dir, true);

    foreach (static::getDirList($dir, true) as $children_dir) {
        $file_list = array_merge($file_list, static::getAllFile($children_dir));
    }

    return $file_list;
};
