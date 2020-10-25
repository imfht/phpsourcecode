<?php

/**
 * 参数接收类
 *
 * @package Comm
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Comm;
abstract class Arg {
    
   
    /**
     * 获取GET数据并过滤
     * 
     * @param string  $name          参数名称
     * @param string  $filter        过滤规则（FILTER_*）
     * @param string  $option        过滤选项
     * @param boolean $must_be_right 是否必需正确（如果传true，不正确抛异常）
     * 
     * @return \mixed
     */
    static public function get($name, $filter = FILTER_DEFAULT, $option = null, $must_be_right = false) {
        $result = filter_input(INPUT_GET, $name, $filter, $option);
        $must_be_right && self::_checkRight($result, $name, $filter);
        return $result;
    }
    
    /**
     * 获取POST数据并过滤
     *
     * @param string  $name          参数名称
     * @param string  $filter        过滤规则（FILTER_*）
     * @param string  $option        过滤选项
     * @param boolean $must_be_right 是否必需正确（如果传true，不正确抛异常）
     *
     * @return \mixed
     */
    static public function post($name, $filter = FILTER_DEFAULT, $option = null, $must_be_right = false) {
        $result = filter_input(INPUT_POST, $name, $filter, $option);
        $must_be_right && self::_checkRight($result, $name, $filter);
        return $result;
    }
    
    /**
     * 获取SERVER数据并过滤
     *
     * @param string  $name          参数名称
     * @param string  $filter        过滤规则（FILTER_*）
     * @param string  $option        过滤选项
     * @param boolean $must_be_right 是否必需正确（如果传true，不正确抛异常）
     *
     * @return \mixed
     */
    static public function server($name, $filter = FILTER_DEFAULT, $option = null, $must_be_right = false) {
//         $result = filter_input(INPUT_SERVER, $name, $filter, $option);
    	$data = isset($_SERVER[$name]) ? $_SERVER[$name] : null;
    	if($data === null) {
    		$result = null;
    	} else {
    		$result = filter_var($data, $filter, $option);
    	}
    	
    	$must_be_right && self::_checkRight($result, $name, $filter);
    	return $result;
        $must_be_right && self::_checkRight($result, $name, $filter);
        return $result;
    }
    
    /**
     * 获取SESSION数据并过滤
     *
     * @param string  $name          参数名称
     * @param string  $filter        过滤规则（FILTER_*）
     * @param string  $option        过滤选项
     * @param boolean $must_be_right 是否必需正确（如果传true，不正确抛异常）
     *
     * @return \mixed
     */
    static public function session($name, $filter = FILTER_DEFAULT, $option = null, $must_be_right = false) {
        $data = isset($_SESSION[$name]) ? $_SESSION[$name] : null;
        if($data === null) {
            $result = null;
        } else {
            $result = filter_var($data, $filter, $option);
        }
        
        $must_be_right && self::_checkRight($result, $name, $filter);
        return $result;
    }
    
    /**
     * 检查是否正确，不正确抛出异常
     * 
     * @param mixed  $data   获取的内容
     * @param string $name   参数名称
     * @param string $filter 过滤方式
     * 
     * @throws \Exception\Arg
     */
    static protected function _checkRight($data, $name, $filter = FILTER_DEFAULT) {
        $is_empty = $data === null;
        if(!$is_empty && $filter !== FILTER_VALIDATE_BOOLEAN) {
            $is_wrong = $data === false;
        }
        
        if($is_empty) {
            throw new \Exception\Arg('Arg empty error.', \Exception\Arg::CODE_NULL, $name);
        }
        
        if($is_wrong) {
            throw new \Exception\Arg('Arg filter error.', \Exception\Arg::CODE_FILTER, $name);
        }
    }
    
} 
