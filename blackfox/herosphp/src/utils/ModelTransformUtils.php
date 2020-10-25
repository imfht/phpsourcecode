<?php
/**
 * 模型转换工具
 * -----------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since v1.2.1
 */

namespace herosphp\utils;

use herosphp\core\Loader;
use herosphp\exception\HeroException;
use herosphp\string\StringUtils;

class ModelTransformUtils
{
    /**
     * map转换为数据模型
     * @param string $class
     * @param array $map
     * @return object|void
     * @throws HeroException
     */
    public static function map2Model($class, $map) {
        if( !$map ) return;
        //字符串类路径
        if( is_string($class) ) {
            $refClass = new \ReflectionClass($class);
            $obj = $refClass->newInstance();
        } else {    //安装对象
            $obj = $class;
            $refClass = new ReflectionClass($obj);
        }
        $methodName = NULL;
        $method = NULL;
        foreach( $map as $key => $value ) {
            $methodName = "set" . ucwords(StringUtils::underline2hump($key));
            if( $refClass->hasMethod($methodName )){
                $method = $refClass->getMethod($methodName);
                try {
                    $method->invoke($obj, $map[$key]);
                } catch (\Exception $e) {
                    throw new HeroException($e->getMessage());
                }
            }
        }
        return $obj;
    }

    /**
     * 模型对象转为map
     * @param $model
     * @return array
     * @throws HeroException
     */
    public static function model2Map($model) {

        $refClass = new \ReflectionClass($model);
        $properties = $refClass->getProperties();
        $map = array();
        foreach ( $properties as $value ) {
            $property = $value->getName();
            if ( strpos($property, '_') ) {
                $property = StringUtils::underline2hump($property); //转换成驼锋格式
            }
            $method = 'get'.ucfirst($property);
            $map[$property] = $model->$method();
        }
        return $map;
    }
}