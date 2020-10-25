<?php
// +----------------------------------------------------------------------
// | PhpStorm.
// +----------------------------------------------------------------------
// | FileName: Login.php
// +----------------------------------------------------------------------
// | Author: King east <1207877378@qq.com>
// +----------------------------------------------------------------------


namespace tpvue\admin\hooks\admin;


use tpvue\admin\model\AdminModel;
use think\db;

class LoginHook
{
    public function success($params)
    {

        list($member, $source) = $params;

        if (is_array($member)) {
            return false;
        }

        // 记录登录SESSION和COOKIES
        $authLogin = array(
            'LoginId'    => $member->id,
            'username'   => $member->username,
            'nickname'   => $member->nickname,
            'mobile'     => $member->mobile,
            'login_time' => $_SERVER['REQUEST_TIME'],
        );

        $auth_login_sign = data_auth_sign($authLogin);

        // 更新登录信息
        $this->member_model = new AdminModel();
        $this->member_model->save([
            'login_num'       => Db::raw('`login_num`+1'),
            'login_ip'        => request()->ip(),
            'login_time'      => time()
        ],['id' => $member['id']]);
        // self::allowField(true)->isUpdate(true)->save($data,['id' => $member['id']]);
        session('user_auth_session', $authLogin);
        session('auth_login_sign', $auth_login_sign);
    }

}