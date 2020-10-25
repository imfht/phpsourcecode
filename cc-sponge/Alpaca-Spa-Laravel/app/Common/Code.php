<?php

namespace App\Common;
/**
 * Code
 * @author Chengcheng
 * @date 2016-10-20 15:50:00
 */
class Code
{
    /**
     * 系统
     */
    const SYSTEM_OK                     = 200;     //正确返回
    const SYSTEM_PARAMETER_NULL         = 2001;    //请求参数为空
    const SYSTEM_PARAMETER_FORMAT_ERROR = 2002;    //请求参数格式错误
    const SYSTEM_ERROR_FIND_NULL        = 2003;    //数据未找到
    const USER_PASSWORD_ERROR           = 2004;    //密码不正确
    const USER_LOGIN_NULL               = 2005;    //用户没有登录
    const USER_POWER_ERROR              = 2006;    //用户没有权限
    const SYSTEM_ERROR                  = 2999;    //系统错误
    const SYSTEM_SAVE_ERROR             = 2007;    //保存失败
    const SYSTEM_USER_INFO_ERROR        = 2008;    //用户数据不存在
    const SYSTEM_CAPTCHA_ERROR          = 2009;    //验证码提交不正确

    /**
     * 用户
     */
    const USER_MOBILE_EXIT      = 3001;     //手机号码已经注册
    const WX_LOGIN_USER_NULL    = 3002;     //微信账号登录成功，但是用户没有注册或者绑定系统账号
    const USER_EMAIL_EXIT       = 3003;     //E-MAIL已经注册
    const USER_EMAIL_ERROR      = 3004;     //E-MAIL不存在,
    const USER_EMAIL_CODE_ERROR = 3005;     //E-MAIL验证码错误

    /**
     * 根据code获取msg
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @param string $code
     * @return mixed
     */
    static function msg($code)
    {
        $message = config("returnCode.zh_CN");
        if (empty($message[$code])) {
            $message[$code] = "";
        }
        return $message[$code];
    }
}
