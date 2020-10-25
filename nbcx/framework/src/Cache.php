<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb;

/**
 * 文件缓存基类
 *
 * @package nb
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/3/30
 *
 * @property  \nb\cache\Driver driver
 */
class Cache extends Component {

    public static function config(){
        return Config::$o->cache;
    }

    /**
     * 检测缓存是否存在
     *
     * @param $name
     * @return mixed
     */
    public static function has($name) {
        return self::driver()->has($name);
    }

    /**
     * 设置一个缓存
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function set($name, $value, $expire = null){
        return self::driver()->set($name,$value,$expire);
    }

    /**
     * 高级写入缓存
     *
     * @param $name 缓存变量名
     * @param $expire 有效时间
     * @param null $value 存储数据
     * @return mixed
     */
    public static function setx($name, $expire, $value = null){
        if (is_null($value)) {
            $value = $expire;
            $expire = null;
        }
        if(is_callable($value)) {
            $value = call_user_func($value);
        }
        else if(is_array($value)) {
            $do = $value[0];
            unset($value[0]);
            if (!strpos($do, '::')) {
                $do = explode(':',$do);
                $class = $do[0];
                $do[0] = new $class();
            }
            $value = call_user_func_array($do,$value);
        }
        self::driver()->set($name,$value,$expire);
        return $value;
    }

    /**
     * 读取缓存数据
     *
     * @param $key
     * @param null $default 默认值
     * @return mixed
     */
    public static function get($key, $default = null) {
        return self::driver()->get($key,$default);
    }

    /**
     * 高级读取缓存
     *
     * @param $name
     * @param $expire 过期时间，也可以是存储数据或回调函数，此时过期时间使用配置时间
     * @param null $value 存储数据，省略此参数，将以$expire代替
     * @return mixed
     */
    public static function getx($name,$expire,$value=null){
        $content = self::get($name);
        if($content !== null) {
            return $content;
        }
        return self::setx($name,$expire,$value);
    }


    /**
     * 修改存储内容
     * @param $key 修改的key值
     * @param $data 修改的内容
     * @return int
     */
    public static function update($name,array $data, $expire = null) {
        return self::driver()->update($name,$data, $expire);
    }

    /**
     * 存储的$content不会被序列化,一般用来存储html页面
     * 当$content为空时,自动调用ob_get_contents获取数据作为缓存
     * @param $key
     * @param null $content
     */
    public static function save($key,$content=null,$timeout=false){

    }

    /**
     * 对应save函数,不对缓存进行反序列化
     * @param $key
     */
    public static function read($key,$content=null,$timeout=false){

    }

    /**
     * 批量删除缓存
     *
     * @param null $tag
     */
    public static function rm($tag = null) {
        return self::driver()->rm($tag);
    }

    /**
     * 根据键名删除对应缓存数据
     *
     * @param null $name
     * @return mixed
     */
    public static function delete($name=null) {
        return self::driver()->delete($name);
    }

    /**
     * 将一个数组写入一个php文件里
     * @param $data
     * @param $fileName 不带后戳的文件名,根路径为path_temp所指向的路径
     * @return int
     */
    public static function php($data, $fileName) {
        $filePath = Config::getx('path_temp') . $fileName.'.php';
        $result = file_put_contents($filePath, "<?php\nreturn " . var_export($data, true) . ";");
        return $result;
    }

}