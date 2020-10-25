<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/28
 * Time: 10:12
 */

namespace naples\lib\base;


 class Service
{


     /** 内部缓存 begin---------------------------------------------------------------------------------------------------------------------- */
     protected static $cache=[]; //静态类缓存，将大大提高函数执行效率。例如多次判断当前IP，其实只要判断一次，然后把IP缓存至此。

     /**
      * 是否缓存了结果
      * @access private
      * @param $key string
      * @return bool
      * @author yuri2
      */
     protected static function isCached($key){
         if (isset(self::$cache[$key]))
             return true;
         else
             return false;
     }

     /**
      * 使用缓存
      * @access private
      * @param $key string
      * @param $value mixed
      * @return mixed
      * @author yuri2
      */
     protected static function useCache($key, $value=FLAG_NOT_SET){
         if ($value!==FLAG_NOT_SET){
             self::$cache[$key]=$value;
             return true;
         }else{
             return self::$cache[$key];
         }
     }
     /** 内部缓存 end---------------------------------------------------------------------------------------------------------------------- */

     protected $configs=[];//配置数组

     /**
     * 配置项的获取和设置
     * @param $key string 键值
     * @param $value mixed 键值
     * @return mixed
     */
     public function config($key=FLAG_NOT_SET,$value=FLAG_NOT_SET){
        if (is_array($key)){
            $this->configs=$key;
            return true;
        }
        if ($key===FLAG_NOT_SET){
            return $this->configs;
        }
        elseif ($value===FLAG_NOT_SET){
            if (isset($this->configs[$key])){
                return $this->configs[$key];
            }else{
                return null;
            }
        }else{
            $this->configs[$key]=$value;
            return true;
        }
    }

}