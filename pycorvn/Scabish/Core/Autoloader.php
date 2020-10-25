<?php
namespace Scabish\Core;

use SCS;

/**
 * Scabish\Core\Autoloader
 * 自动加载类
 * 
 * @author keluo <keluo@focrs.com>
 * @copyright 2016 Focrs, Co.,Ltd
 * @package Scabish
 * @since 2016-12-7
 * @see http://www.php-fig.org/psr/psr-4/
 * @link https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md
 */
class Autoloader {
    
    /**
     * 根据类名加载对应类文件
     * @param string $class 类全名
     * @return boolean
     */
    public static function LoadClass($class) {
        $prefix = $class;
        while(false !== $pos = strrpos($prefix, '\\')) {
            $prefix = substr($class, 0, $pos + 1);
            $relative_class = substr($class, $pos + 1);
            $result = self::LoadMappedFile($prefix, $relative_class);
            if($result) return true;
            $prefix = rtrim($prefix, '\\');
        }
        return false;
    }
    
    /**
     * 加载映射文件
     * @param string $namespace 命名空间前缀
     * @param string $class 类名(无前缀)
     * @return boolean
     */
    protected static function LoadMappedFile($namespace, $class) {
        $namespace = trim($namespace, '\\');
        if(!isset(SCS::Instance()->namespace[$namespace])) return false;
        $maps = SCS::Instance()->namespace[$namespace];
        if(!is_array($maps)) $maps = [$maps];
        $class = str_replace('\\', '/', $class);
        foreach($maps as $map) {
            $file = $map.'/'.ucfirst($class).'.php';
            if(file_exists($file) && is_file($file)) {
                require $file;
                return true;
            }
        }
        
        return false;
    }
}