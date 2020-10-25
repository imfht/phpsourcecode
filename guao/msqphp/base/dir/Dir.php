<?php declare (strict_types = 1);
namespace msqphp\base\dir;

use msqphp\core\traits;

final class Dir
{
    use traits\CallStatic;

    // 扔出异常
    private static function exception(string $message): void
    {
        throw new DirException($message);
    }

    /**
     * @param  string $dir    路径
     * @param  bool   $force  是否强制创建父目录 或 忽略目录是否已经创建
     * @param  int    $code   读写执行代码
     * @param  bool   $force    忽略是否存在, 强制删除
     *
     * @throws DirException
     * @return void
     */

    // 创建目录
    public static function make(string $dir, bool $force = true, int $code = 0755): void
    {
        // 是否目录已存在
        if (is_dir($dir)) {
            // 目录已存在
            $force || static::exception($dir . ' 目录已存在');
        } else {
            // 判断父目录是否存在
            $parent_dir = dirname($dir);
            if (!is_dir($parent_dir)) {
                if ($force) {
                    // 创建
                    static::make($parent_dir, true, $code);
                } else {
                    // 父目录不存在
                    static::exception($dir . ' 父目录不存在');
                }
            }
            // 父目录是否可写
            is_writable($parent_dir) || static::exception($dir . '上级目录' . $parent_dir . '不可写入, 无法创建目录');

            // 创建目录
            if (!mkdir($dir, $code) || !chmod($dir, $code)) {
                static::exception($dir . '未知错误, 无法创建');
            }
        }
    }

    // 删除目录
    public static function delete(string $dir, bool $force = true): void
    {
        // 目录是否存在
        if (is_dir($dir)) {
            $force || static::exception($dir . '目录不存在, 无法删除');
        } else {
            // 是否可操作
            is_writable($dir) || static::exception($dir . '目录不可操作, 无法删除');

            // 如果强制，先清空目录
            $force === true && static::empty($dir);

            // 检测是否为空
            static::isEmpty($dir) || static::exception($dir . ' 目录不为空, 无法删除');

            // 删除目录
            rmdir($dir) || static::exception($dir . '未知错误, 无法删除');
        }
    }
    public static function drop(string $dir, bool $force = true): void
    {
        static::drop($dir, $force);
    }
}
