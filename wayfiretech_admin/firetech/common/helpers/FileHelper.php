<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-04-01 05:26:26
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-18 22:22:07
 */


namespace common\helpers;

use Yii;
use yii\helpers\BaseFileHelper;

/**
 * Class FileHelper
 * @package common\helpers
 * @author chunchun <2192138785@qq.com>
 */
class FileHelper extends BaseFileHelper
{
    /**
     * 检测目录并循环创建目录
     *
     * @param $catalogue
     */
    public static function mkdirs($catalogue)
    {
        if (!file_exists($catalogue)) {
            self::mkdirs(dirname($catalogue));
            mkdir($catalogue, 0777);
        }

        return true;
    }

    /**
     * 写入日志
     *
     * @param $path
     * @param $content
     * @return bool|int
     */
    public static function writeLog($path, $content)
    {
        self::mkdirs(dirname($path));
        @chmod($path, 0777);
        return file_put_contents($path, "\r\n" . $content, FILE_APPEND);
    }

    /**
     * 获取文件夹大小
     *
     * @param string $dir 根文件夹路径
     * @return int
     */
    public static function getDirSize($dir)
    {
        $handle = opendir($dir);
        $sizeResult = 0;
        while (false !== ($FolderOrFile = readdir($handle))) {
            if ($FolderOrFile != "." && $FolderOrFile != "..") {
                if (is_dir("$dir/$FolderOrFile")) {
                    $sizeResult += self::getDirSize("$dir/$FolderOrFile");
                } else {
                    $sizeResult += filesize("$dir/$FolderOrFile");
                }
            }
        }

        closedir($handle);
        return $sizeResult;
    }

    /**
     * 基于数组创建目录
     *
     * @param $files
     */
    public static function createDirOrFiles($files)
    {
        foreach ($files as $key => $value) {
            if (substr($value, -1) == '/') {
                mkdir($value);
            } else {
                file_put_contents($value, '');
            }
        }
    }


    public static function file_read($filename)
    {
        $filename = Yii::getAlias("@attachment/") . $filename;
        if (!is_file($filename)) {
            return false;
        }

        return file_get_contents($filename);
    }

    public static function  file_move($filename, $dest)
    {
        self::mkdirs(dirname($dest));
        if (is_uploaded_file($filename)) {
            move_uploaded_file($filename, $dest);
        } else {
            rename($filename, $dest);
        }
        @chmod($filename, 0777);

        return is_file($dest);
    }


    public static function file_tree($path, $include = array())
    {
        $files = array();
        // return $path . '/' . implode(',', $include) . '';
        if (!empty($include)) {
            $ds = glob($path . '/{' . implode(',', $include) . '}', GLOB_BRACE);
        } else {
            $ds = glob($path . '/*');
        }
        if (is_array($ds)) {
            foreach ($ds as $entry) {
                if (is_file($entry)) {
                    $files[] = $entry;
                }
                if (is_dir($entry)) {
                    $rs = self::file_tree($entry);
                    foreach ($rs as $f) {
                        $files[] = $f;
                    }
                }
            }
        }

        return $files;
    }


    public static function file_tree_limit($path, $limit = 0, $acquired_files_count = 0)
    {
        $files = array();
        if (is_dir($path)) {
            if ($dir = opendir($path)) {
                while (false !== ($file = readdir($dir))) {
                    if (in_array($file, array('.', '..'))) {
                        continue;
                    }
                    if (is_file($path . '/' . $file)) {
                        $files[] = $path . '/' . $file;
                        ++$acquired_files_count;
                        if ($limit > 0 && $acquired_files_count >= $limit) {
                            closedir($dir);

                            return $files;
                        }
                    }
                    if (is_dir($path . '/' . $file)) {
                        $rs = self::file_tree_limit($path . '/' . $file, $limit, $acquired_files_count);
                        foreach ($rs as $f) {
                            $files[] = $f;
                            ++$acquired_files_count;
                            if ($limit > 0 && $acquired_files_count >= $limit) {
                                closedir($dir);

                                return $files;
                            }
                        }
                    }
                }
                closedir($dir);
            }
        }

        return $files;
    }



    public static function file_copy($src, $des, $filter)
    {
        $dir = opendir($src);
        @mkdir($des);
        while (false !== ($file = readdir($dir))) {
            if (('.' != $file) && ('..' != $file)) {
                if (is_dir($src . '/' . $file)) {
                    self::file_copy($src . '/' . $file, $des . '/' . $file, $filter);
                } elseif (!in_array(substr($file, strrpos($file, '.') + 1), $filter)) {
                    copy($src . '/' . $file, $des . '/' . $file);
                }
            }
        }
        closedir($dir);
    }


    public static function rmdirs($path, $clean = false)
    {
        if (!is_dir($path)) {
            return false;
        }
        $files = glob($path . '/*');
        if ($files) {
            $num = count($files);
            $i=0;
            while ($i <= $num) {
                $file = $files[$i];
                is_dir($file) ? self::rmdirs($file) : @unlink($file);
            }
            // foreach ($files as $file) {
            // }
        }

        return $clean ? true : @rmdir($path);
    }


    public static function file_random_name($dir, $ext)
    {
        do {
            $filename = StringHelper::random(30) . '.' . $ext;
        } while (file_exists($dir . $filename));

        return $filename;
    }


    public static function file_delete($file)
    {

        if (empty($file)) {
            return false;
        }

        $file_extension = pathinfo($file, PATHINFO_EXTENSION);
        if (in_array($file_extension, array('php', 'html', 'js', 'css', 'ttf', 'otf', 'eot', 'svg', 'woff'))) {
            return false;
        }
        if (file_exists($file)) {
            @unlink($file);
        }
        if (file_exists(Yii::getAlias("@attachment/") . $file)) {
            @unlink(Yii::getAlias("@attachment/") . $file);
        }

        return true;
    }




    public static function file_lists($filepath, $subdir = 1, $ex = '', $isdir = 0, $md5 = 0, $enforcement = 0)
    {
        static $file_list = array();
        if ($enforcement) {
            $file_list = array();
        }
        $flags = $isdir ? GLOB_ONLYDIR : 0;
        $list = glob($filepath . '*' . (!empty($ex) && empty($subdir) ? '.' . $ex : ''), $flags);
        if (!empty($ex)) {
            $ex_num = strlen($ex);
        }
        foreach ($list as $k => $v) {
            $v = str_replace('\\', '/', $v);
            $v1 = str_replace(Yii::getAlias("@attachment/"), '', $v);
            if ($subdir && is_dir($v)) {
                self::file_lists($v . '/', $subdir, $ex, $isdir, $md5);
                continue;
            }
            if (!empty($ex) && strtolower(substr($v, -$ex_num, $ex_num)) == $ex) {
                if ($md5) {
                    $file_list[$v1] = md5_file($v);
                } else {
                    $file_list[] = $v1;
                }
                continue;
            } elseif (!empty($ex) && strtolower(substr($v, -$ex_num, $ex_num)) != $ex) {
                unset($list[$k]);
                continue;
            }
        }

        return $file_list;
    }




    public static function file_media_content_type($url)
    {

        $file_header = get_headers($url, 1);
        if (empty($url) || !is_array($file_header)) {
            return false;
        }
        switch ($file_header['Content-Type']) {
            case 'application/x-jpg':
            case 'image/jpg':
            case 'image/jpeg':
                $ext = 'jpg';
                $type = 'images';
                break;
            case 'image/png':
                $ext = 'png';
                $type = 'images';
                break;
            case 'image/gif':
                $ext = 'gif';
                $type = 'images';
                break;
            case 'video/mp4':
            case 'video/mpeg4':
                $ext = 'mp4';
                $type = 'videos';
                break;
            case 'video/x-ms-wmv':
                $ext = 'wmv';
                $type = 'videos';
                break;
            case 'audio/mpeg':
                $ext = 'mp3';
                $type = 'audios';
                break;
            case 'audio/mp4':
                $ext = 'mp4';
                $type = 'audios';
                break;
            case 'audio/x-ms-wma':
                $ext = 'wma';
                $type = 'audios';
                break;
            default:
                return false;
                break;
        }

        return array('ext' => $ext, 'type' => $type);
    }
}
