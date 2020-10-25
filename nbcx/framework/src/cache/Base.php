<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\cache;

use nb\Config;

/**
 * Driver
 *
 * @package nb\cache
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/3
 */
class Base extends Driver {

    protected $options = [
        'expire' => 0,
        'path'   => '',
        'ext'    => '.cache',
    ];

    /**
     * 构造函数
     * @param array $options
     */
    public function __construct($options = []) {
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        if(!$this->options['path']) {
            $this->options['path'] = Config::$o->path_temp.'data'.DIRECTORY_SEPARATOR;
        }

        // 创建项目缓存目录
        if (!is_dir($this->options['path'])) {
            if (!mkdir($this->options['path'], 0755, true)) {
                throw new \Exception('Create cache dir is fail!');
            }
        }
    }

    /**
     * 取得变量的存储文件名
     * @access protected
     * @param string $name 缓存变量名
     * @return string
     */
    protected function getCacheKey($name) {
        $filename = str_replace('\\',DIRECTORY_SEPARATOR,$this->options['path'] . $name . $this->options['ext']);
        return $filename;
    }

    /**
     * 取得变量的存储文件名
     * @access protected
     * @param string $name 缓存变量名
     * @return string
     */
    protected function setCacheKey($name) {
        $filename = $this->getCacheKey($name);//$this->options['path'] . $name . $this->options['ext'];
        $dir = dirname($filename);
        // 创建项目缓存目录
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                throw new \Exception('Create cache dir is fail!');
            }
        }
        return $filename;
    }

    /**
     * 判断缓存是否存在
     * @access public
     * @param string $name 缓存变量名
     * @return bool
     */
    public function has($name) {
        return $this->get($name) ? true : false;
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $default 默认值
     * @return mixed
     */
    public function get($name, $default = null) {
        $filename = $this->getCacheKey($name);
        if (!is_file($filename)) {
            return $default;
        }
        $content = file_get_contents($filename);
        if (false !== $content) {
            $expire = (int)substr($content, 8, 12);
            if (0 != $expire && time() > filemtime($filename) + $expire) {
                //缓存过期删除缓存文件
                $this->unlink($filename);
                return $default;
            }
            $content = substr($content, 20, -3);
            $content = unserialize($content);
            return $content;
        }
        return $default;
    }

    /**
     * 写入缓存
     * @param string $name 缓存变量名
     * @param mixed $expire 有效时间 0为永久,当$value为null时，$expire将将作为$value的值
     * @param null $value 缓存变量值
     * @return bool|void
     * @throws \Exception
     */
    public function set($name, $value, $expire) {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        $filename = $this->setCacheKey($name);

        $data = serialize($value);
        $data = "<?php\n//" . sprintf('%012d', $expire) . $data . "\n?>";
        file_put_contents($filename, $data);
    }

    /**
     * 自增缓存（针对数值缓存）
     * @access public
     * @param string $name 缓存变量名
     * @param int $step 步长
     * @return false|int
     */
    public function inc($name, $step = 1) {
        if ($this->has($name)) {
            $value = $this->get($name) + $step;
        }
        else {
            $value = $step;
        }
        return $this->set($name, $value, 0) ? $value : false;
    }

    /**
     * 自减缓存（针对数值缓存）
     * @access public
     * @param string $name 缓存变量名
     * @param int $step 步长
     * @return false|int
     */
    public function dec($name, $step = 1) {
        if ($this->has($name)) {
            $value = $this->get($name) - $step;
        }
        else {
            $value = $step;
        }
        return $this->set($name, $value, 0) ? $value : false;
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function delete($name) {
        return $this->unlink($this->getCacheKey($name));
    }

    /**
     * 清除缓存
     * 支持模糊匹配
     * @access public
     * @param string $tag 标签名
     * @return boolean
     */
    public function rm($pattern = null) {
        if($pattern) {
            $files = $this->options['path'] . $pattern;
        }
        else {
            $files = $this->options['path'] . '/*' . $this->options['ext'];
        }
        return array_map('unlink', glob($files));
    }

    /**
     * 判断文件是否存在后，删除
     * @param $path
     * @return boolean
     */
    private function unlink($path) {
        return is_file($path) && unlink($path);
    }

}
