<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\File;


class File
{
    /**
     * 根据文件名创建文件
     *
     * @param string $file_name
     * @param int $mode
     * @return bool
     */
    public static function mkFile($file_name, $mode = 0644)
    {
        if (!file_exists($file_name)) {
            $file_path = dirname($file_name);
            self::mkDir($file_path, 0755);

            $fp = fopen($file_name, 'w+');
            if ($fp) {
                fclose($fp);
                chmod($file_name, $mode);
                return true;
            }
            return false;
        }
        return true;
    }

    /**
     * 生成四层亿级路径
     *
     * @param int $id
     * @param string $path_name
     * @return string
     *
     * 例: id = 1000189 生成 001/00/01/89
     */
    public static function fourPath($id, $path_name = '')
    {
        $id = (string)abs($id);
        $id = str_pad($id, 9, '0', STR_PAD_LEFT);
        $dir1 = substr($id, 0, 3);
        $dir2 = substr($id, 3, 2);
        $dir3 = substr($id, 5, 2);

        return ($path_name ? $path_name . '/' : '') . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($id, -2) . '/';
    }

    /**
     * 获取文件扩展名
     *
     * @param string $filename 文件名
     * @return string
     */
    public static function ext($filename)
    {
        $file_info = pathinfo($filename);
        return $file_info['extension'];
    }

    /**
     * 创建文件夹
     *
     * @param $path
     * @param int $mode
     */
    public static function mkDir($path, $mode = 0755)
    {
        if (!is_dir($path)) {
            mkdir($path, $mode, true);
        }
    }

    /**
     * 删除文件夹（文件夹及其下面所有文件）
     *
     * @param $dir
     * @return bool
     */
    public static function rmDir($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }
        $handle = opendir($dir);
        while (($file = readdir($handle)) !== false) {
            if ($file != "." && $file != "..") {
                $filename = $dir . DS . $file;
                is_dir($filename) ? self::rmDir($filename) : @unlink($filename);
            }
        }
        if (readdir($handle) == false) {
            closedir($handle);
            @rmdir($dir);
        }
        return true;
    }

    /**
     * 返回规范化的路径
     *
     * @param $path
     * @return string|string[]
     */
    public static function formatPath($path)
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }
}
