<?php

/*
 *  @author myf
 *  @date 2014-11-15 
 *  @Description myfmvc 文件操作类
 *  @web http://www.minyifei.cn
 */

namespace Myf\Mvc;

class File {

    /**
     * 写文件
     * @filename 文件路径
     * @content 内容
     */
    public static function write($filename, $content) {
        $dir = dirname($filename);
        is_dir($dir) or ( createFolders(dirname($dir)) and mkdir($dir, 0777));
        $fp = @fopen($filename, "w");
        if (!$fp) {
            return false;
        } else {
            fwrite($fp, $content);
            fclose($fp);
            return true;
        }
    }

    /**
     * 读取文件
     * @param String $filename 文件绝对路径
     * @return String 内容
     */
    public static function read($filename) {
        $fp = @fopen($filename, "r");
        if (!$fp) {
            return null;
        } else {
            $content = fread($fp, filesize($filename));
            fclose($fp);
            return $content;
        }
    }

    /**
     * 删除文件
     * @param String $filename 文件绝对路径
     * @return boolean 删除成功返回true，返回失败返回false
     */
    public static function delete($filename) {
        $res = @unlink($filename);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 读取文件目录下的文件名
     * @param type $dir
     * @param type $pattern
     * @return type
     */
    public static function dirlist($dir, $pattern = "") {
        $arr = array();
        $dir_handle = opendir($dir);
        if ($dir_handle) {
            // 这里必须严格比较，因为返回的文件名可能是“0”
            while (($file = readdir($dir_handle)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $tmp = realpath($dir . '/' . $file);
                if (is_dir($tmp)) {
                    $retArr = self::dirlist($tmp, $pattern);
                    if (!empty($retArr)) {
                        $arr[] = $file;
                    }
                } else {
                    if ($pattern === "" || preg_match($pattern, $tmp)) {
                        $arr[] = $file;
                    }
                }
            }
            closedir($dir_handle);
        }
        return $arr;
    }

    /**
     * 写入数组缓存
     *
     * @param string $filename
     * @param Array $data 缓存内容
     * @return bool 是否写入成功
     */
    public static function writeArrayCache($filename, $data) {
        $file = CACHE_PATH . "/" . $filename . '.php';
        self::writeArray($file, $data);
    }

    public static function writeArray($file, $data) {
        $dir = dirname($file);
        is_dir($dir) or ( createFolders(dirname($dir)) and mkdir($dir, 0777));
        $data = '<?php return ' . var_export($data, TRUE) . '; ?>';
        return file_put_contents($file, $data);
    }

    /**
     * 读取数组缓存
     * 
     * @param String $filename 文件名
     * @return Array 如果读取成功返回缓存内容, 否则返回NULL
     */
    public static function readArrayCache($filename) {
        $file = CACHE_PATH . "/" . $filename . '.php';
        return self::readArray($file);
    }

    public static function readArray($file) {
        if (file_exists($file)) {
            $data = include $file;
            return $data;
        }
        return array();
    }

}
