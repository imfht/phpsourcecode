<?php
/**
 * Created by PhpStorm.
 * $$Id User: Administrator Date: 15-11-19 Time: 上午9:31
 */


/**
 * 检测验证码
 * @param $code
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 */
function check_verify($code, $id = 1){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}