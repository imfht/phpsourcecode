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

namespace app\common\validate;

use think\Validate;
/**
 * 用户模型类验证器
 * Class Member
 * @author Patrick <contact@uctoo.com>
 */
class Member extends Validate
{
    protected $rule = [
        'signature'  =>'max:100',
        'nickname'  => 'unique:member|require|checkDenyNickname:|checkNickname:|checkNicknameLength:',
        'mobile'  => 'unique:member|require|checkDenyMobile:|checkMobile:',
    ];

    protected $message  =   [
        'signature.max' => '签名最多不能超过100个字符',
        'nickname.unique'   => '用户名不能重复',
        'nickname.require'   => '用户名不能为空',
        'nickname.checkDenyNickname'   => '用户名被禁止注册',
        'nickname.checkNickname'   => '用户名格式错误',
        'nickname.checkNicknameLength'   => '用户名长度需在2到32个字符之间',
    ];

    /**
     * 检测用户名是不是被禁止注册
     * @param  string $nickname 昵称
     * @return boolean          ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyNickname($nickname)
    {
        $denyName = db("Config")->where(array('name' => 'USER_NAME_BAOLIU'))->value('value');
        if ($denyName != '') {
            $denyName = explode(',', $denyName);
            foreach ($denyName as $val) {
                if (!is_bool(strpos($nickname, $val))) {
                    return false;
                }
            }
        }
        return true;
    }

    protected function checkNickname($nickname)
    {
        //如果用户名中有空格，不允许注册
        if (strpos($nickname, ' ') !== false) {
            return false;
        }
        preg_match('/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\s\']+$/', $nickname, $result);

        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * 验证昵称长度
     * @param $nickname
     * @return bool
     * @author 郑钟良<zzl@ourstu.com>
     */
    protected function checkNicknameLength($nickname)
    {
        $length = mb_strlen($nickname, 'utf-8'); // 当前数据长度
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
