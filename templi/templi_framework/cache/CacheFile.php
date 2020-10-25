<?php
/**
 * 缓存类
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date  2013-3-22
 */
namespace framework\cache;
use framework\core\Abnormal,
    framework\Templi;

class CacheFile extends AbstractCache
{
    //private $data_type ='serialize';
    private $_config = array(
        'data_type'=>'array',
        'expire'=>0
    );
    public function __get($name)
    {
        if(isset($this->_config[$name])){
            return $this->_config[$name];
        }
        return null;
    }
    public function __construct(array $config){
        if (!empty($config)){
            $this->_config = array_merge($this->_config, $config);
        }
    }

    /**
     * 读取缓存文件
     * @param string $key 缓存文件名
     * @throws Abnormal
     * @return bool|mixed|null
     */
    public function get($key){
        if(!$key){
            return false;
        }
        $key = $this->buildKey($key);
        $file = Templi::getApp()->getConfig('app_path').'cache/datas/'.$key.'_cache.php';
        if(!file_exists($file)){
            return null;
        }
        if (!is_readable($file)) {
            throw new Abnormal('无读取缓存文件'. $file .'的权限', 500);
        }
        if($this->expire>0){
            if(SYS_TIME-filemtime($file)>$this->expire){
                return null;
            }
        }
        if($this->data_type == 'array'){
            $data = include_once($file);
        }else{
            $data = unserialize(file_get_contents($file));
        }
        return $data;
    }

    /**
     * 设置写入缓存文件
     * @param string $key 文件名
     * @param mixed $value
     * @param int $expire
     * @throws Abnormal
     * @content array or string  缓存的内容
     *
     * @return bool 成功返回 true 失败返回 false
     */
    public function set($key, $value, $expire=null){
        if(!$key || !$value) {
            return false;
        }
        $key= $this->buildKey($key);
        Templi::getApp()->load->helper('Dir.php');
        dir_create(Templi::getApp()->getConfig('app_path').'cache/datas/');
        $file = Templi::getApp()->getConfig('app_path').'cache/datas/'.$key.'_cache.php';
        if (false == is_writable(dirname($file))) {
            throw new Abnormal('无权限写入缓存文件'. $file, 500);
        }
        if ($this->data_type=='array') {
            $file_size = file_put_contents($file, "<?php \nreturn ".var_export($value,true).";\n?>");
        } else {
            $file_size = file_put_contents($file, serialize($value));
        }
        return $file_size ? true : false;
    }
    /**
     * 清除缓存文件
     * @param string $key 文件名
     * @return bool 成功 true 失败 false
     */
    public function clean($key=null){
        if($key){
            return @unlink(Templi::getApp()->getConfig('app_path').'cache/datas/'.$key.'_cache.php');
        }else{
            Templi::getApp()->load->helper('Dir.php');
            return dir_delete(Templi::getApp()->getConfig('app_path').'cache/datas/');
        }
    }
       
}