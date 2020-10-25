<?php
namespace Kernel;

class Cache
{
    protected $tag;
    protected $config = [
        'expire'        => 0,
        'cache_subdir'  => false,
        'prefix'        => '',
        'path'          => RUNTIME_PATH.'cache'.DS,
        'data_compress' => false,
    ];

    protected static $instance;

    public static function instance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 架构函数
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (!empty($config)) {
            $this->config = array_merge($this->config, $config);
        }

        $this->init();
    }

    /**
     * 初始化检查
     * @access private
     * @return boolean
     */
    private function init()
    {
        // 创建项目缓存目录
        if (!is_dir($this->config['path'])) {
            if (mkdir($this->config['path'], 0755, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 取得变量的存储文件名
     * @access protected
     * @param string $name 缓存变量名
     * @return string
     */
    protected function getCacheFileKey($name)
    {
        $name = md5($name);
        if ($this->config['cache_subdir']) {
            // 使用子目录
            $name = substr($name, 0, 2) . DS . substr($name, 2);
        }
        if ($this->config['prefix']) {
            $name = $this->config['prefix'] . DS . $name;
        }
        $filename = $this->config['path'] . $name . '.php';
        $dir      = dirname($filename);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $filename;
    }

    /**
     * 判断缓存是否存在
     * @access public
     * @param string $name 缓存变量名
     * @return bool
     */
    public function has($name)
    {
        return $this->get($name) ? true : false;
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed  $default 默认值
     * @return mixed
     */
    public function get($name, $default = false)
    {
        $filename = $this->getCacheFileKey($name);
        if (!is_file($filename)) {
            return $default;
        }
        $content = file_get_contents($filename);
        if (false !== $content) {
            $expire = (int) substr($content, 8, 12);
            if (0 != $expire && $_SERVER['REQUEST_TIME'] > filemtime($filename) + $expire) {
                //缓存过期删除缓存文件
                $this->unlink($filename);
                return $default;
            }
            $content = substr($content, 20, -3);
            if ($this->config['data_compress'] && function_exists('gzcompress')) {
                //启用数据压缩
                $content = gzuncompress($content);
            }
            $content = unserialize($content);
            return $content;
        } else {
            return $default;
        }
    }

    /**
     * 写入缓存
     * @access public
     * @param string    $name 缓存变量名
     * @param mixed     $value  存储数据
     * @param int       $expire  有效时间 0为永久
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->config['expire'];
        }
        $filename = $this->getCacheFileKey($name);
        if ($this->tag && !is_file($filename)) {
            $first = true;
        }
        $data = serialize($value);
        if ($this->config['data_compress'] && function_exists('gzcompress')) {
            //数据压缩
            $data = gzcompress($data, 3);
        }
        $data   = "<?php\n//" . sprintf('%012d', $expire) . $data . "\n?>";
        $result = file_put_contents($filename, $data);
        if ($result) {
            isset($first) && $this->setTagItem($filename);
            //清除文件状态缓存
            clearstatcache();
            return true;
        } else {
            return false;
        }
    }

    /**
     * 自增缓存（针对数值缓存）
     * @access public
     * @param string    $name 缓存变量名
     * @param int       $step 步长
     * @return false|int
     */
    public function inc($name, $step = 1)
    {
        if ($this->has($name)) {
            $value = $this->get($name) + $step;
        } else {
            $value = $step;
        }
        return $this->set($name, $value, 0) ? $value : false;
    }

    /**
     * 自减缓存（针对数值缓存）
     * @access public
     * @param string    $name 缓存变量名
     * @param int       $step 步长
     * @return false|int
     */
    public function dec($name, $step = 1)
    {
        if ($this->has($name)) {
            $value = $this->get($name) - $step;
        } else {
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
    public function rm($name)
    {
        return $this->unlink($this->getCacheFileKey($name));
    }

    /**
     * 清除缓存
     * @access public
     * @param string $tag 标签名
     * @return boolean
     */
    public function clear($tag = null)
    {
        if ($tag) {
            // 指定标签清除
            $keys = $this->getTagItem($tag);
            foreach ($keys as $key) {
                $this->unlink($key);
            }
            $this->rm('tag_' . md5($tag));
            return true;
        }
        $files = (array) glob($this->config['path'] . ($this->config['prefix'] ? $this->config['prefix'] . DS : '') . '*');
        foreach ($files as $path) {
            if (is_dir($path)) {
                array_map('unlink', glob($path . '/*.php'));
            } else {
                unlink($path);
            }
        }
        return true;
    }

    /**
     * 判断文件是否存在后，删除
     * @param $path
     * @return bool
     * @author byron sampson <xiaobo.sun@qq.com>
     * @return boolean
     */
    private function unlink($path)
    {
        return is_file($path) && unlink($path);
    }

   /**
     * 获取实际的缓存标识
     * @access public
     * @param string $name 缓存名
     * @return string
     */
    protected function getCacheKey($name)
    {
        return $this->config['prefix'] . $name;
    }

    /**
     * 读取缓存并删除
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function pull($name)
    {
        $result = $this->get($name, false);
        if ($result) {
            $this->rm($name);
            return $result;
        } else {
            return null;
        }
    }

    /**
     * 如果不存在则写入缓存
     * @access public
     * @param string    $name 缓存变量名
     * @param mixed     $value  存储数据
     * @param int       $expire  有效时间 0为永久
     * @return mixed
     */
    public function remember($name, $value, $expire = null)
    {
        if (!$this->has($name)) {
            if ($value instanceof \Closure) {
                $value = call_user_func($value);
            }
            $this->set($name, $value, $expire);
        } else {
            $value = $this->get($name);
        }
        return $value;
    }

    /**
     * 缓存标签
     * @access public
     * @param string        $name 标签名
     * @param string|array  $keys 缓存标识
     * @param bool          $overlay 是否覆盖
     * @return $this
     */
    public function tag($name, $keys = null, $overlay = false)
    {
        if (is_null($keys)) {
            $this->tag = $name;
        } else {
            $key = 'tag_' . md5($name);
            if (is_string($keys)) {
                $keys = explode(',', $keys);
            }
            $keys = array_map([$this, 'getCacheKey'], $keys);
            if ($overlay) {
                $value = $keys;
            } else {
                $value = array_unique(array_merge($this->getTagItem($name), $keys));
            }
            $this->set($key, implode(',', $value));
        }
        return $this;
    }

    /**
     * 更新标签
     * @access public
     * @param string $name 缓存标识
     * @return void
     */
    protected function setTagItem($name)
    {
        if ($this->tag) {
            $key       = 'tag_' . md5($this->tag);
            $this->tag = null;
            if ($this->has($key)) {
                $value = $this->get($key);
                $value .= ',' . $name;
            } else {
                $value = $name;
            }
            $this->set($key, $value);
        }
    }

    /**
     * 获取标签包含的缓存标识
     * @access public
     * @param string $tag 缓存标签
     * @return array
     */
    protected function getTagItem($tag)
    {
        $key   = 'tag_' . md5($tag);
        $value = $this->get($key);
        if ($value) {
            return explode(',', $value);
        } else {
            return [];
        }
    }

}
