<?php

namespace app\admin\behavior;

use think\Request;
use think\Config;
use think\Hook;
/**
 * Description of CheckIp
 * 检测用户访问的ip
 * @author static7
 */
class CheckIp {

    use \traits\controller\Jump;
    /**
     * 检测用户IP
     */

    public function run() {
        $allow_ip = Config::get('admin_allow_ip');
        $ip = Request::instance()->ip();
        if (!is_administrator() && $allow_ip) {
            !in_array($ip, explode(',', $allow_ip)) && $this->error('禁止访问');// 检查IP地址访问
        }
        \think\Log::record("[ 访问者IP ]：" . $ip);
    }

}
