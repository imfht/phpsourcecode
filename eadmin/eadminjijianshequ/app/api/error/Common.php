<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\api\error;

class Common
{

    public static $passwordError = [API_CODE_NAME => 1010001, API_MSG_NAME => '登录密码错误'];

    public static $usernameOrPasswordEmpty = [API_CODE_NAME => 1010002, API_MSG_NAME => '用户名或密码不能为空'];

    public static $registerFail = [API_CODE_NAME => 1010003, API_MSG_NAME => '注册失败'];

    public static $oldOrNewPassword = [API_CODE_NAME => 1010004, API_MSG_NAME => '旧密码或新密码不能为空'];

    public static $changePasswordFail = [API_CODE_NAME => 1010005, API_MSG_NAME => '密码修改失败'];

    public static $upFail = [API_CODE_NAME => 1010005, API_MSG_NAME => '上传信息失败'];

}
