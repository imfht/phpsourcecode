<?php
/**
 * @package Yaf
 * @author 李枨煊<lcx165@gmail.com> (DOC Only)
 */
abstract class Yaf_Config_Abstract {

    protected $_config;

    protected $_readonly;

    /**
     * 寻找只读配置
     *
     * @return bool
     */
    abstract public function readonly() ;

    /**
     * 转换为数组
     *
     * @return array
     */
    abstract public function toArray() ;

    /**
     * Setter
     *
     * @return Yaf_Config_Abstract
     */
    abstract public function set() ;

    /**
     * Getter
     *
     * @param string $name 
     * @param mixed $value 
     *
     * @return mixed
     */
    abstract public function get($name, $value) ;


}