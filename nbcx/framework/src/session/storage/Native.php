<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\session\storage;

use nb\Config;
use SessionHandlerInterface;

/**
 * Memcached
 *
 * @package nb\session\storage
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/29
 */
class Native implements SessionHandlerInterface {

    private $gcTime = 1800;
    private $config;
    private $filename;
    private $path;


    public function __construct($options) {
        $this->gcTime = $options['expire'] * 60;
        $this->path   =  $options['path']?:Config::$o->path_temp.'session'.DIRECTORY_SEPARATOR;
        //if (!empty($config['cache_expire'])) {
        //    $this->gcTime = $config['expire'] * 60;
        //}
        //$this->config = $config;
    }

    public function open($path, $sid) {
        $this->filename = $this->getFileName($path, $sid);
    }

    public function close() {
        return true;
    }

    public function gc($time) {
        $path = $this->path;
        $files = self::tree($path);
        foreach ($files as $file) {
            if (false !== strpos($file, 'sess_')) {
                if (fileatime($file) < (time() - $this->gcTime)) {
                    unlink($file);
                }
            }
        }
        return true;
    }

    public function read($sid) {
        $this->filename = $this->getFileName($sid);
        if (is_file($this->filename)) {
            $content = file_get_contents($this->filename);
            if (strlen($content) < 10) {
                unlink($this->filename);
                return false;
            }
            $time = floatval(substr($content, 0, 10));
            if ($time < (time() - $this->gcTime)) {
                unlink($this->filename);
                return false;
            }
            return substr($content, 10);
        }
    }

    public function write($sid, $data) {
        $this->filename = $this->getFileName($sid);
        $content = time() + $this->gcTime . $data;
        file_put_contents($this->filename, $content);
        return true;
    }

    public function destroy($sid) {
        $this->filename = $this->getFileName($sid);
        if (is_file($this->filename)) {
            unlink($this->filename);
            return false;
        }
    }

    private function getPath() {
    //    return path. 'session_tmp';
    }

    private function getFileName($sid) {
        $path = $this->path;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        if (!empty($this->config['callback']) && is_callable($this->config['callback'])) {
            return call_user_func($this->config['callback'], $path, $sid);
        }

        return $path . 'sess_' . $sid;
    }

    /**
     * 递归获取目录下的文件
     * @param $dir
     * @param string $filter
     * @param array $result
     * @param bool $deep
     * @return array
     */
    public function tree($dir, $filter = '', &$result = array(), $deep = false) {
        $files = new \DirectoryIterator($dir);
        foreach ($files as $file) {
            if ($file->isDot()) {
                continue;
            }

            $filename = $file->getFilename();
            //过滤文件移动到下面  change by ahuo 2013-09-11 16:23
            //if (!empty($filter) && !\preg_match($filter, $filename)) {
            //  continue;
            //}

            if ($file->isDir()) {
                $this->tree($dir . DS . $filename, $filter, $result, $deep);
            }
            else {
                if (!empty($filter) && !\preg_match($filter, $filename)) {
                    continue;
                }
                if ($deep) {
                    $result[$dir] = $filename;
                }
                else {
                    $result[] = $dir . DS . $filename;
                }
            }
        }
        return $result;
    }
}
