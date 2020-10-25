<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2017 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace app\ucenter\validate;

use think\Validate;
/**
 * 用户模型类验证器
 * Class UcenterMember
 * @author Patrick <contact@uctoo.com>
 */
class UcenterMember extends Validate
{
    protected $rule = [
        'email'  =>'email',
        'username'  => 'unique:ucenter_member|require|checkDenyUsername:|checkUsername:|checkUsernameLength:',
        'mobile'  => 'unique:ucenter_member|require|checkDenyMobile:|checkMobile:',
    ];

    protected $message  =   [
        'email.email' => '邮箱格式不正确',
        'username.unique'   => '用户名不能重复',
        'username.require'   => '用户名不能为空',
        'nickname.checkDenyUsername'   => '用户名被禁止注册',
        'nickname.checkUsername'   => '用户名格式错误',
        'nickname.checkUsernameLength'   => '用户名长度需在2到32个字符之间',
        'mobile.unique'   => '手机号不能重复',
        'mobile.require'   => '手机号不能为空',
        'mobile.checkDenyMobile'   => '手机号被禁止注册',
        'mobile.checkMobile'   => '手机号格式错误',
    ];
    protected $scene = [
        'updateUsername'  =>  ['username'],
        'updateMobile'  =>  ['mobile'],
        'adminEditUsername'  =>  ['username'=> 'unique:ucenter_member|checkDenyUsername:|checkUsername:|checkUsernameLength:'], //管理员编辑场景可以改成空
        'adminEditMobile'  =>  ['mobile'=>'unique:ucenter_member|checkDenyMobile:|checkMobile:'],
    ];
    /**
     * 检测用户名是不是被禁止注册
     * @param  string $username 用户名
     * @return boolean          ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyUsername($username)
    {
        $denyName = db("Config")->where(array('name' => 'USER_NAME_BAOLIU'))->value('value');
        if ($denyName != '') {
            $denyName = explode(',', $denyName);
            foreach ($denyName as $val) {
                if (!is_bool(strpos($username, $val))) {
                    return false;
                }
            }
        }
        return true;
    }

    protected function checkUsername($username)
    {
        //如果用户名中有空格，不允许注册
        if (strpos($username, ' ') !== false) {
            return false;
        }
        preg_match('/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\s\']+$/', $username, $result);

        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * 验证昵称长度
     * @param $username
     * @return bool
     * @author 郑钟良<zzl@ourstu.com>
     */
    protected function checkUsernameLength($username)
    {
        $length = mb_strlen($username, 'utf-8'); // 当前数据长度
        if ($length < modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG') || $length > modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG')) {
            return false;
        }
        return true;
    }

    /**
     * 检测手机是不是被禁止注册
     * @param  string $mobile 手机
     * @return boolean        ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyMobile($mobile)
    {
        return true; //TODO: 暂不限制，下一个版本完善
    }

    protected function checkMobile($mobile)
    {
        //如果用户名中有空格，不允许注册
        if (strpos($mobile, ' ') !== false) {
            return false;
        }
        preg_match('/^1[3|4|5|7|8][0-9]\d{8}$/', $mobile, $result);

        if (!$result) {
            return false;
        }
        return true;
    }
}
