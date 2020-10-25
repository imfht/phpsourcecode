<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/11
 * Time: 9:08
 */

namespace naples;

/**
 * 自动加载类处理
 */
class AutoLoad
{
    private static $configs=[]; //手动指定加载数组，请在configs/autoload.php定义
    private static $logFileLoaded=[]; //记录加载了哪些文件

    //注册自动加载函数
    public static function register(){
        self::$configs=require PATH_NAPLES.'/configs/autoload.php';
        spl_autoload_register(['\naples\AutoLoad','manual']);
        spl_autoload_register(['\naples\AutoLoad','psr4']);
        require PATH_VENDOR.'/autoload.php';
    }

    //psr4加载 PATH_ROOT PATH_EXTEND 都作为根命名空间
    private static function psr4($class){
        if (class_exists($class,false)){return;}
        if ($class{0}!='\\'){
            $class='\\'.$class;
        }
        $vendor=PATH_ROOT;
        $path=$vendor.str_replace('\\','/',$class).'.php';
        if (file_exists($path)){
            self::$logFileLoaded[]=$path;
            require $path;
            return;
        }else{
            $vendor=PATH_EXTEND;
            $path=$vendor.str_replace('\\','/',$class).'.php';
            if (file_exists($path)){
                self::$logFileLoaded[]=$path;
                require $path;
                return;
            }
        }
    }

    //加载手动指定的路径
    private static function manual($class){
        if (class_exists($class,false)){return;}
        foreach (self::$configs as $k=>$v){
            if ($class===$k){
                if (is_file($v)){
                    self::$logFileLoaded[]=$v;
                    require $v;
                    return;
                }
            }
        }
    }
    
    //返回文件加载记录数组
    public static function getLogFileLoaded(){
        return self::$logFileLoaded;
    }

    /**
     * 传入类名，尝试找到自动加载路径
     * @param string $className
     * @return bool|string
     */
    public static function tryFindClass($className){
        if(function_exists('cache') and !isFlagNotSet(cache('Autoload_tryFindClass_'.$className))){
            return cache('Autoload_tryFindClass_'.$className);
        }else{
            //manual
            foreach (self::$configs as $k=>$v){
                if ($className===$k){
                    if (is_file($v)){
                        self::$logFileLoaded[]=$v;
                        if (function_exists('cache')){
                            cache('Autoload_tryFindClass_'.$className,$v);
                        }
                        return $v;
                    }
                }
            }
            //psr4
            if ($className{0}!='\\'){
                $className='\\'.$className;
            }
            $vendor=PATH_ROOT;
            $path=$vendor.str_replace('\\','/',$className).'.php';
            if (is_file($path)){
                if (function_exists('cache')){
                    cache('Autoload_tryFindClass_'.$className,$path);
                }
                return $path;
            }
            if (function_exists('cache')){
                cache('Autoload_tryFindClass_'.$className,false);
            }
            return false;
        }
    }

}