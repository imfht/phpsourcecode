<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/12/5
 * Time: 14:06
 */

namespace naples\lib\caches;

use naples\lib\base\Service;

/** 仿tp5的本地缓存类 */
class fileCache extends Service
{
    protected $options = [
        'expire'        => 0,
        'cache_subdir'  => false,
        'prefix'        => '',
        'path'          => '',
        'data_compress' => false,
    ];

    /**
     * 初始化检查
     * @access private
     * @return boolean
     */
    public function init()
    {
        $this->options=array_merge($this->options,$this->configs);
        // 创建项目缓存目录
        if (!is_dir($this->options['path'])) {
            if (mkdir($this->options['path'], 0755, true)) {
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
    protected function getCacheKey($name)
    {
        $name = md5($name);
        if ($this->options['cache_subdir']) {
            // 使用子目录
            $name = substr($name, 0, 2) . DS . substr($name, 2);
        }
        if ($this->options['prefix']) {
            $name = $this->options['prefix'] . DS . $name;
        }
        $filename = $this->options['path'] . $name . '.php';
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
        $filename = $this->getCacheKey($name);
        if (!is_file($filename)) {
            return $default;
        }
        $content = file_get_contents($filename,LOCK_SH);
        if (false !== $content) {
            $expire = (int) substr($content, 8, 12);
            if (0 != $expire && $_SERVER['REQUEST_TIME'] > filemtime($filename) + $expire) {
                //缓存过期删除缓存文件
                $this->unlink($filename);
                return $default;
            }
            $content = substr($content, 20, -3);
            if ($this->options['data_compress'] && function_exists('gzcompress')) {
                //启用数据压缩
                $content = gzuncompress($content);
            }
            $content = unserialize($content);
            return $content;
        } else {
            return $default;
        }
    }
    
    /** 按文件名直接读取 */
    private function getFromRawName($name, $default = false)
    {
        $filename = $name;
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
            if ($this->options['data_compress'] && function_exists('gzcompress')) {
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
            $expire = $this->options['expire'];
        }
        $filename = $this->getCacheKey($name);

        $data = serialize($value);
        if ($this->options['data_compress'] && function_exists('gzcompress')) {
            //数据压缩
            $data = gzcompress($data, 3);
        }
        $data   = "<?php\n//" . sprintf('%012d', $expire) . $data . "\n?>";
        $result = file_put_contents($filename, $data,LOCK_EX);
        if ($result) {
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
        return $this->unlink($this->getCacheKey($name));
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

    /** 清理过期缓存 */
    public function cleanOverTime(){
        $dirCache=$this->options['path'];
        $obj=$this;
        \Yuri2::ergodicDir($dirCache,function ($file) use(&$dirCache,$obj){
            $fileFullPath=$dirCache.'/'.$file;
            $obj->getFromRawName($fileFullPath);
        });
    }

}