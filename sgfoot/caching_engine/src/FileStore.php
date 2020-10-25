<?php

namespace SgIoc\Cache;

/**
 * 文件存储引擎
 * User: freelife2020@163.com
 * Date: 2018/3/16
 * Time: 12:51
 */

class FileStore extends StoreAbstract
{

    protected $directory;

    public function __construct($config)
    {
        if (!is_null($config)) {
            $this->config = array_merge($this->config, $config);
        }
        if (isset($config['open']) && !$config['open']) {
            throw new \Exception('file switch does not set true');
        }
        if (!isset($config['path'])) {
            throw new \Exception('The ' . __METHOD__ . ' engine configure item does not have a path node.');
        }
        $this->directory = $config['path'];
        if (!is_dir($this->directory)) {
            if (!@mkdir($this->directory, '0777', true)) {
                throw new \Exception('Not a directory');
            }
        }
        if (!$this->is_writeable($this->directory)) {
            throw new \Exception('The directory has no permissions to write.');
        }
    }

    /**
     * 获取详情
     * @return array
     */
    public function info()
    {
        return array('config' => $this->config, 'cache-files' => glob($this->directory . '*/*'));
    }

    /**
     * 判断键是否存在
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return $this->get($key) ? true : false;
    }

    /**
     * 获取内容
     * @param $key
     * @param bool $default
     * @return bool|mixed
     */
    public function get($key, $default = null)
    {
        $filename = $this->getFileName($key);
        if (!is_file($filename)) {
            return $this->value($default);
        }
        $content = file_get_contents($filename);
        if ($content !== false) {
            $minutes = (int)substr($content, 0, 12);
            if ($minutes != 0 && $_SERVER['REQUEST_TIME'] > filemtime($filename) + $minutes * 60) {
                $this->unlink($filename);
                return $this->value($default);
            }
            $content = substr($content, 12);
            if ($this->config['is_zip']) {
                $content = gzuncompress($content);
            }
            return $this->unserialize($content);
        }
        return $this->value($default);
    }

    /**
     * 获取&删除
     * @param $key
     * @return bool|mixed
     */
    public function pull($key)
    {
        $value = $this->get($key);
        $this->forget($key);
        return $value;
    }

    /**
     * 不存在则创建,成功返回true;存在则返回 false
     * @param $key
     * @param $value
     * @param $minutes
     * @return bool
     */
    public function add($key, $value, $minutes = 60)
    {
        if ($this->has($key)) {
            return false;
        }
        return $this->put($key, $value, $minutes);
    }

    /**
     * 设置,存在则覆盖,不存在则创建,支持匿名函数
     * @param $key string 键值
     * @param $value mixed 数据
     * @param int $minutes 分钟
     * @return bool
     */
    public function put($key, $value, $minutes = null)
    {
        if (is_null($minutes)) {
            $minutes = $this->config['expired'];
        }
        $filename = $this->getFileName($key);
        $data     = $this->serialize($value);
        if ($this->config['is_zip']) {
            $data = gzcompress($data, $this->config['zip_level']);
        }
        $data   = sprintf('%012d', $minutes) . $data;
        $result = file_put_contents($filename, $data);
        if ($result) {
            clearstatcache();
            return true;
        }
        return false;
    }

    /**
     * 永久存储
     * @param $key
     * @param $value
     * @return bool
     */
    public function forever($key, $value)
    {
        return $this->put($key, $value, 0);
    }

    /**
     * 自增缓存
     * @param $key
     * @param int $value
     * @return bool|int|mixed
     */
    public function increment($key, $value = 1)
    {
        if ($this->has($key)) {
            $value = $this->get($key) + $value;
        }
        return $this->put($key, $value) === true ? $value : false;
    }

    /**
     * 自减缓存
     * @param $key
     * @param int $value
     * @return bool|int|mixed
     */
    public function decrement($key, $value = 1)
    {
        if ($this->has($key)) {
            $value = $this->get($key) - $value;
        }
        return $this->put($key, $value) === true ? $value : false;
    }

    /**
     * 清除指定缓存
     * @param $key
     * @return bool
     */
    public function forget($key)
    {
        if ($this->has($key)) {
            $filename = $this->getFileName($key);
            return $this->unlink($filename);
        }
        return false;
    }

    /**
     * 清理所有的缓存
     * @return bool
     */
    public function flush()
    {
        $this->removeDir($this->directory);
        @mkdir($this->directory, 0777);
        return true;
    }

    /**
     * 如果缓存存在则返回已有的,不存在进行缓存,返回数据
     * @param $key string 键值
     * @param $minutes int 分钟
     * @param mixed $callback 匿名函数
     * @return bool|mixed
     */
    public function remember($key, $minutes, $callback)
    {
        if (!is_null($value = $this->get($key))) {
            return $value;
        }
        $this->put($key, $value = $this->value($callback), $minutes);
        return $value;
    }

    /**
     * 永久缓存
     * @param $key
     * @param mixed $callback
     * @return bool|mixed
     */
    public function rememberForever($key, $callback)
    {
        return $this->remember($key, 0, $callback);
    }

    public function getKey($key)
    {
        return $this->config['preFix'] . $key;
    }

    public function close()
    {

    }

    /**
     * 获取存储文件名,全路径
     * @param $key
     * @return string
     */
    protected function getFileName($key)
    {
        $name     = md5($this->getKey($key));
        $filename = $this->directory . substr($name, 0, 2) . DIRECTORY_SEPARATOR . $name;
        $dir      = dirname($filename);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $filename;
    }


    /**
     * 判断目录是否可写
     * @param $file
     * @return int
     */
    protected function is_writeable($file)
    {
        if (is_dir($file)) {
            $dir = $file;
            if ($fp = @fopen("$dir/test.txt", 'w')) {
                @fclose($fp);
                @unlink("$dir/test.txt");
                $writeable = 1;
            } else {
                $writeable = 0;
            }
        } else {
            if ($fp = @fopen($file, 'a+')) {
                @fclose($fp);
                $writeable = 1;
            } else {
                $writeable = 0;
            }
        }
        return $writeable;
    }

    /**
     * 判断文件是否存在后，删除
     * @param $path
     * @return bool
     * @return boolean
     */
    protected function unlink($path)
    {
        return is_file($path) && unlink($path);
    }

    /**
     * 递归删除目录
     * @param $dirName
     * @return bool
     */
    protected function removeDir($dirName)
    {
        if (!is_dir($dirName)) {
            return false;
        }
        $handle = @opendir($dirName);
        while (($file = @readdir($handle)) !== false) {
            if ($file != '.' && $file != '..') {
                $dir = $dirName . '/' . $file;
                is_dir($dir) ? $this->removeDir($dir) : @unlink($dir);
            }
        }
        closedir($handle);
        return is_dir($dirName) && rmdir($dirName);
    }
}