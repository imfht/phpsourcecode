<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/1
 * Time: 19:17
 */

namespace fastwork;


class Config
{
    /**
     * 配置参数
     * @var array
     */
    protected $config = [];

    /**
     * 配置文件目录
     * @var string
     */
    protected $path;

    /**
     * 配置文件后缀
     * @var string
     */
    protected $ext = '.php';


    /**
     * 构造方法
     * @access public
     */
    public function __construct($path = '')
    {
        $this->path = $path;
    }

    public static function __make(Fastwork $fastwork)
    {
        $path = $fastwork->getConfigPath();
        return new static($path);
    }

    /**
     * 检测配置是否存在
     * @access public
     * @param  string $name 配置参数名（支持多级配置 .号分割）
     * @return bool
     */
    public function has($name)
    {
        return !is_null($this->get($name));
    }

    /**
     * 获取配置参数
     * @param string $keys 参数名 格式：文件名.参数名1.参数名2....
     * @param null $default 错误默认返回值
     *
     * @return mixed|null
     */
    public function get($keys = null, $default = NULL)
    {
        if (empty($keys)) {
            return $this->config;
        }
        $keys = array_filter(explode('.', strtolower($keys)));
        // 无参数时获取所有
        $file = array_shift($keys);
        $config = $this->pull($file);
        while ($keys) {
            $key = array_shift($keys);
            if (!isset($config[$key])) {
                $config = $default;
                break;
            }
            $config = $config[$key];
        }

        return $config;
    }

    /**
     * 获取一级配置
     * @access public
     * @param  string $name 一级配置名
     * @return array
     */
    public function pull($name)
    {
        $file = strtolower($name);
        if (!isset($this->config[$file])) {
            $path = $this->path . $file . $this->ext;
            if (!is_file($path)) {
                return NULL;
            }
            $this->config[$file] = require $path;
        }
        return $this->config[$file];
    }


    /**
     * 加载配置文件（多种格式）
     * @access public
     * @param  string $file 配置文件名
     * @param  string $name 一级配置名
     * @return mixed
     */
    public function load($file, $name = '')
    {
        if (is_file($file)) {
            $filename = $file;
        } elseif (is_file($this->path . $file . $this->ext)) {
            $filename = $this->path . $file . $this->ext;
        }

        if (isset($filename)) {
            return $this->loadFile($filename, $name);
        }

        return $this->config;
    }

    protected function loadFile($file, $name)
    {
        $name = strtolower($name);
        return $this->set(include $file, $name);
    }

    /**
     * 设置配置参数 name为数组则为批量设置
     * @access public
     * @param  string|array $name 配置参数名（支持三级配置 .号分割）
     * @param  mixed $value 配置值
     * @return mixed
     */
    public function set($name, $value = null)
    {
        if (is_string($name)) {
            $name = explode('.', $name, 3);
            if (count($name) == 1) {
                $this->config[strtolower($name[0])] = $value;
            } else if (count($name) == 2) {
                $this->config[strtolower($name[0])][$name[1]] = $value;
            } else {
                $this->config[strtolower($name[0])][$name[1]][$name[2]] = $value;
            }
            return $value;
        } elseif (is_array($name)) {
            // 批量设置
            if (!empty($value)) {
                if (isset($this->config[$value])) {
                    $result = array_merge($this->config[$value], $name);
                } else {
                    $result = $name;
                }

                $this->config[$value] = $result;
            } else {
                $result = $this->config = array_merge($this->config, $name);
            }
        } else {
            // 为空直接返回 已有配置
            $result = $this->config;
        }

        return $result;
    }


    /**
     * 移除配置
     * @access public
     * @param  string $name 配置参数名（支持三级配置 .号分割）
     * @return void
     */
    public function remove($name)
    {
        if (false === strpos($name, '.')) {
            $name = $this->prefix . '.' . $name;
        }

        $name = explode('.', $name, 3);

        if (count($name) == 2) {
            unset($this->config[strtolower($name[0])][$name[1]]);
        } else {
            unset($this->config[strtolower($name[0])][$name[1]][$name[2]]);
        }
    }

    /**
     * 重置配置参数
     * @access public
     * @param  string $prefix 配置前缀名
     * @return void
     */
    public function reset($prefix = '')
    {
        if ('' === $prefix) {
            $this->config = [];
        } else {
            $this->config[$prefix] = [];
        }
    }

    /**
     * 设置配置
     * @access public
     * @param  string $name 参数名
     * @param  mixed $value 值
     * @return mixed
     */
    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * 获取配置参数
     * @access public
     * @param  string $name 参数名
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * 检测是否存在参数
     * @access public
     * @param  string $name 参数名
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

    // ArrayAccess
    public function offsetSet($name, $value)
    {
        $this->set($name, $value);
    }

    public function offsetExists($name)
    {
        return $this->has($name);
    }

    public function offsetUnset($name)
    {
        $this->remove($name);
    }

    public function offsetGet($name)
    {
        return $this->get($name);
    }
}