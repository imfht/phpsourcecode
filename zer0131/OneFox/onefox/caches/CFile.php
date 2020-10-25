<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 文件缓存类
 */

namespace onefox\caches;

use onefox\Cache;
use onefox\Config;

class CFile extends Cache {

    public function __construct() {
        $this->options = Config::get('cache.file');
        if (!$this->options) {
            $this->options = [
                'path' => APP_PATH . DS . 'Cache', //缓存路径
                'expire' => 0, //有效期，单位秒，0表示长久有效
                'prefix' => 'onefox_'//缓存文件名前缀
            ];
        }
        if (substr($this->options['path'], -1) != '/') {
            $this->options['path'] .= '/';
        }
        // 创建应用缓存目录
        if (!is_dir($this->options['path'])) {
            mkdir($this->options['path']);
        }
    }

    /**
     * 取得变量的存储文件名
     *
     * @access private
     * @param string $name 缓存变量名
     * @return string
     */
    private function _filename($name) {
        $name = md5($name);
        #使用子目录
        $dir = '';
        for ($i = 0; $i < 1; $i++) {
            $dir .= $name{$i} . '/';
        }
        if (!is_dir($this->options['path'] . $dir)) {
            mkdir($this->options['path'] . $dir, 0755, true);
        }
        $filename = $dir . $this->options['prefix'] . $name;
        return $this->options['path'] . $filename;
    }

    public function get($name) {
        $filename = $this->_filename($name);
        if (!is_file($filename)) {
            return false;
        }
        $content = file_get_contents($filename);
        if (false !== $content) {
            $expire = (int)substr($content, 0, 12);
            if ($expire != 0 && time() > filemtime($filename) + $expire) {
                #缓存过期删除缓存文件
                unlink($filename);
                return false;
            }
            $content = substr($content, 12);
            $content = unserialize($content);
            return $content;
        } else {
            return false;
        }
    }

    public function set($name, $value, $expire = null) {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        $filename = $this->_filename($name);
        $data = serialize($value);
        $data = sprintf('%012d', $expire) . $data;
        $result = file_put_contents($filename, $data);
        if ($result) {
            clearstatcache();//清除文件缓存状态
            return true;
        } else {
            return false;
        }
    }

    public function rm($name, $ttl = 0) {
        if (is_file($this->_filename($name))) {
            return unlink($this->_filename($name));
        }
        return true;
    }

    public function clear() {
        $path = $this->options['path'];
        $files = scandir($path);
        if ($files) {
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && is_dir($path . $file)) {
                    array_map('unlink', glob($path . $file . '/*.*'));
                } elseif (is_file($path . $file)) {
                    unlink($path . $file);
                }
            }
            return true;
        }
        return false;
    }
}
