<?php

/**
 * @author: caipeichao
 */

/**
 * 参数数量任意，返回第一个非空参数
 * @return mixed|null
 */
function alt() {
    for($i = 0 ; $i < func_num_args(); $i++) {
        $arg = func_get_arg($i);
        if($arg) {
            return $arg;
        }
    }
    return null;
}

function array_gets($array, $fields) {
    $result = array();
    foreach($fields as $e) {
        if(array_key_exists($e, $array)) {
            $result[$e] = $array[$e];
        }
    }
    return $result;
}

function saveMobileInSession($mobile) {
    session_start();
    session('send_sms', array('mobile'=>$mobile));
}

function getMobileFromSession() {
    return session('send_sms.mobile');
}