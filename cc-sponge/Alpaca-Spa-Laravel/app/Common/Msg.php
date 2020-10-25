<?php

namespace App\Common;

/**
 * Msg
 * @author Chengcheng
 * @date 2016-10-20 15:50:00
 */
class Msg
{
    /**
     * 系统
     */
    const SYSTEM_OK                     = '操作成功';
    const SYSTEM_PARAMETER_NULL         = '请求参数:[%s]不能为空';
    const SYSTEM_PARAMETER_FORMAT_ERROR = '请求参数:[%s]格式错误';
    const SYSTEM_ERROR_FIND_NULL        = '没有找到要修改或者删除的数据';
    const USER_PASSWORD_ERROR           = '密码不正确';
    const USER_LOGIN_NULL               = '用户没有登录';
    const USER_POWER_ERROR              = '用户没有权限';
    const SYSTEM_ERROR                  = '系统错误';
    const SYSTEM_SAVE_ERROR             = '保存失败';
    const SYSTEM_USER_INFO_ERROR        = '用户数据不存在';

    /**
     * 用户
     */
    const USER_MOBILE_EXIT      = '手机号码已经注册';
    const WX_LOGIN_USER_NULL    = '微信账号登录成功，但是用户没有注册或者绑定系统账号';
    const USER_EMAIL_EXIT       = 'E-MAIL已经注册';
    const USER_EMAIL_ERROR      = 'E-MAIL不存在';
    const USER_EMAIL_CODE_ERROR = 'E-MAIL验证码错误';

}