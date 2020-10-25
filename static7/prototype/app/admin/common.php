<?php

use think\Session;
use think\Config;
use think\Cache;
use think\Db;
use think\Cookie;

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_login() {
    $user = Session::get('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return Session::get('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 检测当前用户是否为管理员
 * @param null $uid
 * @return bool true-管理员，false-非管理员
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_administrator($uid = null) {
    $user_id = $uid ?? is_login();
    return (int) $user_id && ((int) $user_id === (int) Config::get('user_administrator'));
}

/**
 * 检测验证码
 * @param $code 验证码ID
 * @param  integer $id id
 * @return bool 检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1) {
    $verify = new \Verify\verify();
    return $verify->check($code, $id);
}

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @param string $key 默认密钥
 * @return string
 */
function ucenter_md5($str, $key = 'calm7.com') {
    return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pk
 * @param string $pid parent标记字段
 * @param string $child level标记字段
 * @param int $root 根
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = [];
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = [];
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = & $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = & $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = & $refer[$parentId];
                    $parent[$child][] = & $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 获取数据的状态操作
 * @param int $status
 * @return string 状态文字 ，false 未获取到
 * @author huajie <banhuajie@163.com>
 */
function show_status_op($status) {
    switch (true) {
        case ($status == "禁用" || $status == "否"): return 0;
        case ($status == "启用" || $status == "是") : return 1;
        case ($status == "删除" || $status == -1) : return -1;
        default : return null;
    }
}

/**
 * 二维数组重新降维成一位数组
 * @param array $array 二维数组
 * @author staitc7 <static7@qq.com>
 * @return array|null
 */
function one_dimensional($array) {
    if (empty($array)) {
        return null;
    }
    $tmp = [];
    foreach ($array as $k => $v) {
        $tmp[$v['name']] = $v['id'];
    }
    return $tmp;
}


/**
 *  分析枚举类型配置值
 *  格式 a:名称1,b:名称2
 * @param $string 配置值
 * @return array
 */
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if (strpos($string, ':')) {
        $value = [];
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }
    return $value;
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    for ($i = 0; $size >= 1024 && $i < 5; $i++) {
        $size /= 1024;
    }
    return $size . $delimiter . $units[$i];
}

/**
 * 获取行为类型
 * @param intger $type 类型
 * @param bool $all 是否返回全部类型
 * @author huajie <banhuajie@163.com>
 * @return array|mixed
 */
function get_action_type($type, $all = false) {
    $list = [
        1 => '系统',
        2 => '用户',
    ];
    if ($all) {
        return $list;
    }
    return $list[$type];
}

/**
 * 获取行为数据
 * @param int|string $id 行为id
 * @internal param string $field 需要获取的字段
 * @author huajie <banhuajie@163.com>
 * @return bool|null
 */
function get_action($id = 0) {
    if (empty($id)) {
        return false;
    }
    $list = Cache::get('action_title_list');
    if (empty($list)) {
        $list = Db::name('Action')->where('status', 'eq', 1)->column('title', 'id');
        if ($list) {
            Cache::set('action_title_list', $list);
            $data = $list[(int) $id];
        }
    }
    $data = $list[(int) $id];
    return $data ?? null;
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @author staitc7 <static7@qq.com>
 * @return string       用户昵称
 */
function get_nickname($uid = 0) {
    $list = Cache::get('sys_user_nickname_list'); /* 获取缓存数据 */
    if ($list) {
        if (isset($list[$uid])) { // 查找用户信息
            return $name = $list[$uid];
        }
    }
    $info = Db::name('Member')->where('uid', $uid)->value('nickname'); //调用接口获取用户信息
    if ($info === false) {
        $name = '';
    }
    $name = $info;
    /* 缓存用户 */
    $count = count($list);
    $max = Config::get('key.user_max_cache');
    while ($count-- > $max) {
        array_shift($list);
    }
    Cache::set('sys_user_nickname_list', $list);
    return $name;
}

/**
 * 检测头像
 * @param int $user_id 用户ID
 * @author staitc7 <static7@qq.com>
 * @return mixed|string
 */
function portrait($user_id = null) {
    $id = empty($user_id) ? is_login() : $user_id;
    $info = Cookie::get("user_{$id}", "portrait_");
    if ($info) {
        return $info;
    } else {
        $portrait_id = Db::name('Member')->where('uid', $id)->value('portrait');
        $path = (int)$portrait_id > 0 ? get_cover($portrait_id) : TPL_PATH . "admin/images/default.png";
        Cookie::set("user_{$id}", $path, ['prefix' => 'portrait_', 'expire' => 86400]);
        return $path;
    }
}

/**
 * 获取文档封面图片
 * @param int $cover_id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 * @author huajie <banhuajie@163.com>
 */
function get_cover($cover_id = 0, $field = 'path') {
    $picture = (int) $cover_id > 0 ?
            Db::name('Picture')->where(['status' => 1, 'id' => $cover_id])->value($field) :
            TPL_PATH . 'admin/images/null.gif'; //返回默认图片
    return $picture;
}

/**
 * 获取文件名
 * @param int $file_id 文件id
 * @param string $field 字段
 * @author staitc7 <static7@qq.com>
 * @return mixed|string
 */
function get_file($file_id = 0, $field = 'name') {
    $file = (int) $file_id > 0 ? Db::name('File')->where(['id' => $file_id])->value($field) : '未知文件';
    return $file;
}

/**
 * 检查$pos(推荐位的值)是否包含指定推荐位$contain
 * @param int $pos 推荐位的值
 * @param int $contain 指定推荐位
 * @return bool true 包含 ， false 不包含
 * @author huajie <banhuajie@163.com>
 */
function check_position($pos = 0, $contain = 0) {
    if (empty($pos) || empty($contain)) {
        return false;
    }
    $res = $pos & $contain; //将两个参数进行按位与运算，不为0则表示$contain属于$pos
    return ($res !== 0) ? true : false;
}

/**
 * 检查该分类是否允许发布内容
 * @param int $id 分类id
 * @param string $field 字段
 * @param bool $direct 直接返回
 * @return bool|int|mixed
 * @author static7 <static7@qq.com>
 */
function checkCategory(int $id = 0, string $field = 'id', bool $direct = false) {
    $tmp = Db::name('Category')->where(['id' => (int) $id, 'status' => 1])->value($field);
    if ($direct) {
        return $tmp;
    }
    if (is_numeric($tmp)) {
        $info = (int) $tmp > 0 ? 1 : 0;
    } elseif (is_array($tmp)) {
        $info = true; //TODO 暂时不处理
    } else {
        $info = $tmp ? 1 : 0;
    }
    return $info;
}
