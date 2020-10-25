<?php

// +----------------------------------------------------------------------
// | HopePHP
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.wispx.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: WispX <i@wispx.cn>
// +----------------------------------------------------------------------

// [ 文件操作类 ]

namespace hope;

class File
{
    /**
     * 创建文件夹
     *
     * @param string $path 文件夹路径
     */
    public static function createFolder($path)
    {
        if (!file_exists($path)) {
            self::createFolder(dirname($path));
            mkdir($path, 0777);
        }
    }


    /**
     * 得到指定目录里的信息
     * @param $path 目录
     * @return array|null
     */
    public static function getFolder($path)
    {
        if (!is_dir($path)) return null;

        $path = rtrim($path, '/') . '/';

        $array = array('file' => array(), 'folder' => array());
        $dir_obj = opendir($path);

        while ($dir = readdir($dir_obj)) {
            if ($dir != '.' && $dir != '..') {
                $file = $path . $dir;
                if (is_dir($file)) {
                    $array['folder'][] = $dir;
                } elseif (is_file($file)) {
                    $array['file'][] = $dir;
                }

            }
        }
        closedir($dir_obj);
        return $array;

    }


    /**
     * 删除文件
     *
     * @param string $path 文件路径
     */
    public static function delFile($path)
    {
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    /**
     * 删除目录
     * @param $path 目录
     */
    public static function deleteDir($path)
    {
        if (!is_dir($path)) return;

        $path = rtrim($path, '/') . '/';

        $dir_obj = opendir($path);

        while ($dir = readdir($dir_obj)) {
            if ($dir != '.' && $dir != '..') {
                $file = $path . $dir;
                if (is_dir($file)) {
                    self::deleteDir($file);
                } elseif (is_file($file)) {
                    unlink($file);
                }

            }
        }
        closedir($dir_obj);
        rmdir($path);
    }

    /**
     * 复制目录
     *
     * @param string $source 要复制的目录地址
     * @param string $destination 目标目录地址
     * @param int $child 是否复制子目录
     * @return bool
     */
    public static function xCopy($source, $destination, $child = 1)
    {
        if (!is_dir($source)) {
            echo("Error:the $source is not a direction!");
            return 0;
        }
        if (!is_dir($destination)) {
            mkdir($destination, 0777);
        }
        $handle = dir($source);
        while ($entry = $handle->read()) {
            if (($entry != ".") && ($entry != "..")) {
                if (is_dir($source . "/" . $entry)) {
                    if ($child) self::xCopy($source . "/" . $entry, $destination . "/" . $entry, $child);
                } else {
                    copy($source . "/" . $entry, $destination . "/" . $entry);
                }
            }
        }
        return 1;
    }

    /**
     * 复制文件
     * @param string $source 要复制的目录地址
     * @param string $path 目标目录地址
     * @return null
     */
    public static function copyFile($source, $path)
    {
        $path = str_replace('\\', '/', $path);
        $arr = explode('/', $path);
        array_pop($arr);
        $folder = join('/', $arr);
        if (!is_dir($folder)) {
            self::createFolder($folder);
        }
        if (is_file($source)) {
            copy($source, $path);
        }
    }


    /**
     * 创建文件
     *
     * @param unknown_type $file 文件名
     * @param string $content 文件内容
     */
    public static function createFile($file, $content = '')
    {
        $folder = dirname($file);
        if (!is_dir($folder)) {
            self::createFolder($folder);
        }

        file_put_contents($file, $content);
    }

    /**
     * 获取文件后缀名
     * @param $file 文件名
     * @return mixed
     */
    public static function getExtension($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }
}