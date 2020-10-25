<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\util;

/**
 * FileManager class file
 * 文件管理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FileManager.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.util
 * @since 1.0
 */
class FileManager
{
    /**
     * 重命名一个文件或目录
     * @param string $oldName
     * @param string $newName
     * @return boolean
     */
    public function rename($oldName, $newName)
    {
        if (!$this->fileExists($oldName) || $this->fileExists($newName)) {
            return false;
        }

        @rename($oldName, $newName);
        if (!$this->fileExists($oldName) && $this->fileExists($newName)) {
            return true;
        }

        return false;
    }

    /**
     * 拷贝文件，不能拷贝目录
     * 如果目标文件已存在，将会被覆盖；如果目标文件是目录，则返回false
     * @param string $source
     * @param string $dest
     * @return boolean
     */
    public function copy($source, $dest)
    {
        if (!$this->isFile($source) || $this->isDir($dest)) {
            return false;
        }

        $ret = @copy($source, $dest);
        return $ret;
    }

    /**
     * 新建目录，如果目录存在，则改变文件权限
     * @param string $directory
     * @param integer $mode 文件权限，8进制
     * @param boolean $recursive 递归创建所有目录
     * @return boolean
     */
    public function mkDir($directory, $mode = 0777, $recursive = false)
    {
        if ($this->isFile($directory)) {
            return false;
        }

        if ($this->isDir($directory)) {
            $filePerms = $this->filePerms($directory);
            if ($filePerms === $mode) {
                return true;
            }

            return $this->chmod($directory, $mode);
        }

        @mkdir($directory, $mode, $recursive);
        return $this->isDir($directory);
    }

    /**
     * 删除目录，会递归删除目录中所有文件
     * @param string $directory
     * @return boolean
     */
    public function rmDir($directory)
    {
        if ($this->isFile($directory)) {
            return false;
        }

        if (!$this->isDir($directory)) {
            return true;
        }

        $dh = opendir($directory);
        while (($fileName = readdir($dh)) !== false) {
            if ($fileName === '.' || $fileName === '..' ) {
                continue;
            }

            $fileName = $directory . DIRECTORY_SEPARATOR . $fileName;
            if ($this->isDir($fileName)) {
                $this->rmDir($fileName);
            }
            else {
                $this->unlink($fileName);
            }
        }

        closedir($dh);
        @rmdir($directory);
        return !$this->isDir($directory);
    }

    /**
     * 删除文件
     * @param string $fileName
     * @return boolean
     */
    public function unlink($fileName)
    {
        if ($this->isDir($fileName)) {
            return false;
        }

        if ($this->isFile($fileName)) {
            @unlink($fileName);
            return !$this->isFile($fileName);
        }

        return true;
    }

    /**
     * 获取目录中的文件
     * @param string $directory
     * @param boolean $recursive 是否递归获取所有目录
     * @return array
     */
    public function scanDir($directory, $recursive = false)
    {
        $ret = array();
        if (!$this->isDir($directory)) {
            return $ret;
        }

        $dh = opendir($directory);
        while (($fileName = readdir($dh)) !== false) {
            if ($fileName === '.' || $fileName === '..' ) {
                continue;
            }

            $ret[] = $fileName = $directory . DIRECTORY_SEPARATOR . $fileName;
            if ($recursive && $this->isDir($fileName)) {
                $ret[] = $this->scanDir($fileName, $recursive);
            }
        }

        closedir($dh);
        return $ret;
    }

    /**
     * 判断目录是否为空，如果目录不存在或不是目录，则返回null
     * @param string $directory
     * @return boolean
     */
    public function isEmpty($directory)
    {
        if (!$this->isDir($directory)) {
            return null;
        }

        $ret = true;
        $dh = opendir($directory);
        while (($fileName = readdir($dh)) !== false) {
            if ($fileName !== '.' && $fileName !== '..' ) {
                $ret = false;
                break;
            }
        }

        closedir($dh);
        return $ret;
    }

    /**
     * 检查文件或目录是否存在
     * @param string $fileName
     * @return boolean
     */
    public function fileExists($fileName)
    {
        return file_exists($fileName);
    }

    /**
     * 判断给定文件名是否是一个目录
     * @param string $fileName
     * @return boolean
     */
    public function isDir($fileName)
    {
        return is_dir($fileName);
    }

    /**
     * 判断给定文件名是否是一个文件
     * @param string $fileName
     * @return boolean
     */
    public function isFile($fileName)
    {
        return is_file($fileName);
    }

    /**
     * 判断给定文件名是否可读
     * @param string $fileName
     * @return boolean
     */
    public function isReadable($fileName)
    {
        return is_readable($fileName);
    }

    /**
     * 判断给定的文件名是否可写
     * @param string $fileName
     * @return boolean
     */
    public function isWriteable($fileName)
    {
        return is_writeable($fileName);
    }

    /**
     * 改变文件权限
     * @param string $fileName
     * @param integer $mode 文件权限，八进制
     * @return boolean
     */
    public function chmod($fileName, $mode)
    {
        $ret = @chmod($fileName, $mode);
        return $ret;
    }

    /**
     * 获取文件权限，返回十进制整型
     * @param string $fileName
     * @return integer|false
     */
    public function filePerms($fileName)
    {
        $ret = fileperms($fileName);
        if ($ret) {
            $ret = substr(sprintf('%o', $ret), -4);
            $ret = octdec($ret);
        }

        return $ret;
    }
}
