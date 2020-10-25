<?php

use think\facade\Env;

/**
 * 引用系统自定义常量
 */
if (is_file(Env::get('app_path') . 'api/constants.php')) {
    include_once Env::get('app_path') . 'api/constants.php';
}

if (!function_exists('message')) {

    /**
     * 消息数组函数
     * @param string $msg 提示语
     * @param bool $success 是否成功
     * @param array $data 结果数据
     * @return array 返回消息对象
     * @author 牧羊人
     * @date 2019/4/5
     */
    function message($msg = "系统繁忙，请稍候再试", $success = false, $data = [], $code = 0)
    {
        $result = array("success" => $success, "msg" => $msg, "data" => $data);
        if ($success) {
            $result['code'] = 10000;
        } else {
            $result['code'] = $code ? $code : 90000;
        }
        return $result;
    }
}
