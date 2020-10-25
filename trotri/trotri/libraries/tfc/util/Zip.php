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

use tfc\ap\ErrorException;

/**
 * Zip class file
 * ZIP方式压缩和解压文件，支持Linux(Shell实现)和WINNT(WinRAR.exe实现)系统
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Zip.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.util
 * @since 1.0
 */
class Zip
{
    /**
     * @var string 解压和压缩方式前缀
     */
    protected $_methodPre = '';

    /**
     * @var string WINNT系统解压和压缩文件工具地址
     */
    protected $_rarPath = 'C:\Program Files\WinRAR\WinRAR.exe';

    /**
     * 构造方法：初始化操作系统类型，如果是WINNT操作系统，需要指定压缩工具地址
     * @param string|null $rarPath
     * @throws ErrorException 如果不是Linux和WINNT系统，抛出异常
     * @throws ErrorException 如果是WINNT系统，但是解压和压缩文件工具的地址不存在，抛出异常
     */
    public function __construct($rarPath = null)
    {
        if (is_string($rarPath)) {
            $this->_rarPath = $rarPath;
        }

        switch (PHP_OS) {
            case 'Linux':
                $this->_methodPre = 'lin';
                break;
            case 'WINNT':
                $this->_methodPre = 'win';
                if (!is_file($this->_rarPath)) {
                    throw new ErrorException(sprintf(
                        'Zip OS is WINNT, but rar path "%s" is not a valid file.', $this->_rarPath
                    ));
                }
                break;
            default:
                throw new ErrorException(sprintf(
                    'Zip PHP_OS "%s" must be Linux or WINNT.', PHP_OS
                ));
        }
    }

    /**
     * 压缩，自动判断操作系统类型
     * @param string $source
     * @param string $zip
     * @return string
     */
    public function pack($source, $zip)
    {
        $method = $this->_methodPre . 'Pack';
        return $this->$method($source, $zip);
    }

    /**
     * 解压，自动判断操作系统类型
     * @param string $zip
     * @param string $toPath
     * @return string
     */
    public function unPack($zip, $toPath)
    {
        $method = $this->_methodPre . 'UnPack';
        return $this->$method($zip, $toPath);
    }

    /**
     * 在Linux系统打包
     * @param string $source
     * @param string $zip
     * @return string
     */
    public function linPack($source, $zip)
    {
        $paths = pathinfo($source);
        $cmd = sprintf('cd %s && zip -r %s %s', $paths['dirname'], $zip, $paths['filename']);
        return exec($cmd);
    }

    /**
     * 在Linux系统解包
     * @param string $zip
     * @param string $toPath
     * @return string
     */
    public function linUnPack($zip, $toPath)
    {
        $cmd = sprintf('unzip -o -d %s %s', $toPath, $zip);
        return exec($cmd);
    }

    /**
     * 在WINNT系统打包
     * @param string $source
     * @param string $zip
     * @return string
     */
    public function winPack($source, $zip)
    {
        $cmd = sprintf('"%s" a -as -r -ep1 "%s" "%s"', $this->_rarPath, $zip, $source);
        $ret = exec($cmd);
        if (!$ret) {
            $cmd2 = '"' . $cmd . '"';
            $ret = exec($cmd2);
        }

        return $ret;
    }

    /**
     * 在WINNT系统解包
     * @param string $zip
     * @param string $toPath
     * @return string
     */
    public function winUnPack($zip, $toPath)
    {
        $cmd = sprintf('"%s" e "%s" "%s"', $this->_rarPath, $zip, $toPath);
        $ret = exec($cmd);
        if (!$ret) {
            $cmd2 = '"' . $cmd . '"';
            $ret = exec($cmd2);
        }

        return $ret;
    }
}
