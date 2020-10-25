<?php declare (strict_types = 1);
namespace msqphp\base\file;

use msqphp\base\dir\Dir;
use msqphp\core\traits;

final class File
{
    use traits\CallStatic;

    // 扔出异常
    private static function exception(string $message): void
    {
        throw new FileException($message);
    }

    /**
     * @param  string       $file     文件路径
     * @param  bool         $force    是否强制(即忽略当前文件或目录情况,强制执行,提现在会创建目录文件,覆盖文件等等)
     * @param  int          $len      长度
     * @param  string|int   $content  内容
     * @throws FileException
     *
     * @return void
     */

    // 删除文件
    public static function drop(string $file, bool $force = true): void
    {
        static::delete($file, $force);
    }
    public static function delete(string $file, bool $force = true): void
    {
        if (is_file($file)) {
            // 不可写,异常
            !is_writable($file) && static::exception($file . '文件不可写,无法删除');
            // 错误,异常
            !unlink($file) && static::exception($file . '未知错误,无法删除');
        } else {
            // 强制或异常
            $force || static::exception($file . '不存在,无法删除');
        }
    }

    // 读取指定长度的文件内容
    public static function read(string $file, int $len): string
    {
        // 存在或异常
        is_file($file) || static::exception($file . '不存在,无法读取');

        // 可读或异常
        is_readable($file) || static::exception($file . '无法操作,无法读取');

        // 读取内容
        $fp = fopen($file, 'r');
        (false === $content = fread($fp, $len)) && static::exception($file . '未知错误,无法读取');
        fclose($fp);
        unset($fp);

        return (string) $content;
    }

    // 获取文件内容
    public static function get(string $file): string
    {
        // 存在或异常
        is_file($file) || static::exception($file . '不存在,无法读取');

        // 可读或异常
        is_readable($file) || static::exception($file . '无法操作,无法读取');

        // 无法读取
        (false === $content = file_get_contents($file)) && static::exception($file . '未知错误,无法读取');

        return (string) $content;
    }

    // 追加文件内容
    public static function append(string $file, $content, bool $force = true): void
    {
        // 文件不存在
        if (!is_file($file)) {
            if ($force) {
                static::write($file, $content, true);
            } else {
                static::exception($file . '不存在,无法追加内容');
            }
        } else {
            // 文件存在
            is_writable($file) || static::exception($file . '父目录,无法写入');

            if (false === file_put_contents($file, (string) $content, FILE_APPEND | LOCK_EX)) {
                static::exception($file . '未知错误,无法追加内容');
            }
        }
    }

    // 重写文件|保存文件
    public static function save(string $file, $content, bool $force = true): void
    {
        static::write($file, $content, $force, $code);
    }
    public static function write(string $file, $content, bool $force = true): void
    {
        if (is_file($file)) {
            is_writable($file) || static::exception($file . '无法操作,无法写入');
        } else {
            // 父目录
            $parent_dir = dirname($file);
            // 目录不存在
            if (!is_dir($parent_dir)) {
                // 错
                if (!$force) {
                    static::exception($file . '父目录不存在,无法写入');
                } else {
                    // 创建
                    Dir::make($parent_dir, true);
                }
            } else {
                // 可写或异常
                is_writable($parent_dir) || static::exception($file . '父目录,无法写入');
            }
        }
        (false !== file_put_contents($file, (string) $content, LOCK_EX)) || static::exception($file . '未知错误,无法写入');
    }
}
