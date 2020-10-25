<?php
/**
 * 参数检测相关的助手类
 * User: huangjacky
 * Date: 15/4/2015
 * Time: 06:38
 */
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('Int')){
    /**
     * 整数强制类型转换
     * @param $val mixed 需要转换的参数
     * @return int 转换后的值
     */
    function Int($val){
        return (int) $val;
    }
}

if(!function_exists('reg_check')){
    /**
     * 参数正则检测
     * @param $val mixed 被检测的参数
     * @param $pattern string 正则
     * @return int
     */
    function reg_check($val, $pattern){
        return preg_match($pattern, $val);
    }
}

if(! function_exists('lt')){
    function lt($val, $bound){
        return $val < $bound;
    }
}

if(! function_exists('lte')){
    function lte($val, $bound){
        return $val <= $bound;
    }
}

if(! function_exists('gt')){
    function gt($val, $bound){
        return $val > $bound;
    }
}

if(! function_exists('gte')){
    function gte($val, $bound){
        return $val >= $bound;
    }
}

if(! function_exists('between')){
    function between($val, $low, $high){
        return $low<$val && $val<$high;
    }
}

if(! function_exists('string_length')){
    function string_length($val, $l1, $l2=0){
        $len = strlen($val);
        if($l2 == 0)
            return $len == $l1;
        else
            return $len > $l1 && $len < $l2;
    }
}

/**
 * 当参数校验错误的时候执行的回调函数
 * @param $pname string 校验错误的参数名
 */
function _error_callback($pname){
    $CI = & get_instance();
    $error_callback = $CI->config->item('params_check')['param_error_callback'];
    if(! empty($error_callback)){
        if(is_string($error_callback)){//字符串
            call_user_func($error_callback, $pname);
        }else{
            $error_callback($pname);//匿名函数
        }
    }	else{ //调用系统的默认函数
        show_404();
    }
}

/**
 * 解析字符串函数,并执行相应的值
 * @param $str string 字符串表示的函数
 * @param $v mixed 函数调用的值
 * @return mixed 函数执行后返回的值
 */
function _parse_func($str, $v){
    $fs = explode(':', $str);
    $fn = $fs[0];
    if (count($fs) ==  1)
        $fs = array($v);
    else{
        $fs = explode(',', $fs[1]);
        array_splice($fs,0, 0, array($v));
    }
    return call_user_func_array($fn, $fs);
}


function _check_params($rparams, $check_params, &$params, $use_index=true){
    foreach($rparams as $r){
        $pname = $r->name;
        $pidx = $r->getPosition();
        $poptional = $r->isOptional();
        if(! array_key_exists($pname, $check_params))
            continue;
        $check = $check_params[$pname]['check'];
        $error = false;
        if($use_index){
            if(count($params) <= $pidx){
                continue;
            }else
                $pv = $params[$pidx];
        }else{
            if(! array_key_exists($pname, $params)){
                    continue;
            }else
                $pv = $params[$pname];
        }
        if ($error){//参数校验错误
            _error_callback( $pname);
            return false;
        }
        //根据规则来判断
        foreach($check as $f){
            if (! _parse_func($f, $pv)){
                _error_callback($pname);
                return false;
            }
        }
        //根据过滤来执行过滤
        $filter = $check_params[$pname]['filter'];
        if(! empty($filter)){
            if(is_string($filter)){
                $new_v = call_user_func($filter, $pv);
            }else{
                $new_v = $filter($pv);
            }
        }
        if($use_index)
            $params[$pidx] = $new_v;
        else
            $params[$pname] = $new_v;
    }
    return true;
}

function _entry($CFG, $class, $method, $params){
    $CFG->load('params_check', true);
    $check_params = $CFG->config['params_check']['params'];
    //如果有参数或者有判断规则
    if(count($check_params) > 0 && (count($params)||count($_REQUEST))){
        //当前方法的一些反射信息
        $rmethod = new ReflectionMethod($class, $method);
        //参数列表
        $rparams = $rmethod->getParameters();
        //如果用户需要判断参数,那么加载一些辅助内置判断函数
        if(count($rparams)){
            return _check_params($rparams,$check_params,$params, true) && _check_params($rparams,$check_params,$_REQUEST, false);
        }

    }
    return false;
}