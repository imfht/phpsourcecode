<?php
/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

/**
 * 文本日志记录
 *
 * @param $content
 * @param string $type
 * @param string $dir
 */
function write_log($content = null , $type = '', $dir = "./") {
    $now = date('Y-m-d H:i:s');
    $file = $dir . 'Log_' . date('Y-m-d'). ".json";
    $content = json_encode($content);
    file_put_contents($file, "\n".$now ."\n".$type . " ".$content, FILE_APPEND);
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login() {
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 检测当前登录用户是否管理员
 *
 * @param array $uids
 * @return bool
 */
function check_permission($uids = array()) {
    if (is_administrator()) {
        return true;
    }
    if (in_array(is_login(), $uids)) {
        return true;
    }
    return false;
}

/**
 * 检测当前用户是否为管理员
 * @param null $uid
 * @return boolean true-管理员，false-非管理员
 */
function is_administrator($uid = null) {
    $uid = is_null($uid) ? is_login() : $uid;
    $admin_uids = explode(',', C('USER_ADMINISTRATOR'));//调整验证机制，支持多管理员，用,分隔
    //dump($admin_uids);exit;
    return $uid && (in_array(intval($uid), $admin_uids));//调整验证机制，支持多管理员，用,分隔
}

/**
 * 数据签名认证
 * @param  array $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data) {
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * 检测是否合法的EMAIL
 *
 * @param $str
 * @return bool
 */
function valid_email($str) {
    return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
}