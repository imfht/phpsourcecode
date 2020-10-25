<?php
/**
 * 系统常用函数库 原则上这里只提供一个调用入口不要写太多逻辑
 * Date: 2016/9/24 0024
 * Time: 9:35
 * Author: 李华胜 lihuasheng@wapwei.com
 */
use db\Db;
use Yaf\Registry;
function test() {
    echo 'test';
}

/**
 * 缓存管理
 *
 * @param mixed $name 缓存名称，如果为数组表示进行缓存设置
 * @param mixed $value 缓存值
 * @param mixed $options 缓存参数
 * @param string $tag 缓存标签
 *
 * @return mixed
 */
function cache($name, $value = '', $options = null, $tag = null) {
    return Helper::cache($name, $value, $options, $tag);
}

/**
 * Session管理
 *
 * @param string|array $name session名称，如果为数组表示进行session设置
 * @param mixed $value session值
 * @param string $prefix 前缀
 *
 * @return mixed
 */
function session($name, $value = '', $prefix = null) {
    return Helper::session($name, $value, $prefix);
}

/**
 * cookie管理
 *
 * @param $name
 * @param string $value
 * @param null $option
 * @return mixed
 */
function cookie($name, $value = '', $option = null) {
    return Helper::cookie($name, $value, $option);
}

/**
 * 实例化数据库类
 *
 * @param string $name 操作的数据表名称（不含前缀）
 * @param array|string $config 数据库配置参数
 * @param bool $force 是否强制重新连接
 *
 * @return \think\db\Query
 */
function db($name = '', $config = [], $force = true) {
    return Db::connect($config, $force)->name($name);
}


/**
 * 获得当前用户信息
 * @return array
 */
function user() {
    $simulationLogin = isset(Registry::get('config')->simulationLogin) ? Registry::get('config')->simulationLogin : 0;
    if ($simulationLogin) {
        return [
            'id'         => 1,
            'userid'     => 'tttlkkkl',
            'department' => [1,2],
            'name'       => '李华',
            'position'   => 'CEO',
            'mobile'     => 18025434221
        ];
    } else {
        return session('user','','login');
    }

}

/**
 * 返回公司信息
 * @return array
 */
function company() {
    $simulationLogin = isset(Registry::get('config')->simulationLogin) ? Registry::get('config')->simulationLogin : 0;
    if ($simulationLogin) {
        return [
            'id'         => 1,
            'name'       => 'aK47',
            'corpid'     => 'wx4fa7d40737be7934',
            'corpsecret' => 'smZfzJCzqgJCMwbUiFHaBMVllUwLsJYU0XDTN9VbNjYA4PlBX-j2fkQoUiWFx0Ar'
        ];
    } else {
        return session('company','','login');
    }
}

function pre($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}
