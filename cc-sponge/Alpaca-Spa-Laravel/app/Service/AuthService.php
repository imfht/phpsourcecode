<?php
namespace App\Service;

use App\Common\Visitor;
use App\Models\PublicToken;
use App\Models\UserMember;
use App\Common\Msg;
use App\Common\Code;
use App\Models\WechatUser;
use Illuminate\Support\Facades\DB;
use Lib\Wechat\WeChat;

/**
 * Auth
 * @author Chengcheng
 * @date 2016-10-19 15:50:00
 */
class AuthService
{
    /**
     * 用户登录-通过Email-Password
     * @author Chengcheng
     * @param array $requestData
     * @return array
     */
    public static function loginEmail($requestData)
    {
        //0 预制返回结果
        $result         = array();
        $result["code"] = Code::SYSTEM_ERROR;
        $result["msg"]  = Msg::SYSTEM_ERROR;

        //1 判断邮箱是否存在
        $member = UserMember::model()->where('email', $requestData['email'])->first();
        if (!$member) {
            $result["code"] = Code::USER_EMAIL_ERROR;     //手机号码不存在
            $result["msg"]  = Msg::USER_EMAIL_ERROR;
            return $result;
        }

        //2 验证密码是否正确
        if (!password_verify($requestData['passwd'], $member->passwd)) {
            $result["code"] = Code::USER_PASSWORD_ERROR;
            $result["msg"]  = Msg::USER_PASSWORD_ERROR;
            return $result;
        }

        //3 保存用户登录信息

        //获取用户系统账号信息
        $memberLoginResult = UserMember::model()->login($member->id);
        if ($memberLoginResult['code'] != Code::SYSTEM_OK) {
            return $memberLoginResult;
        }

        //5 登录成功，返回结果
        $result["code"] = Code::SYSTEM_OK;
        $result["msg"]  = Msg::SYSTEM_OK;
        $result["data"] = $memberLoginResult['data'];
        return $result;
    }

    /**
     * 用户登录-Test
     * @author Chengcheng
     * @param array $requestData
     * @return array
     */
    public static function loginTest($requestData)
    {
        //0 预制返回结果
        $result         = array();
        $result["code"] = Code::SYSTEM_ERROR;
        $result["msg"]  = Msg::SYSTEM_ERROR;

        //1 判断邮箱是否存在
        $member = UserMember::model()->where('userid', $requestData['email'])->first();
        if (!$member) {
            $result["code"] = Code::USER_EMAIL_ERROR;     //手机号码不存在
            $result["msg"]  = "没有找到帐号";
            return $result;
        }

        //3 保存用户登录信息

        //获取用户系统账号信息
        $memberLoginResult = UserMember::model()->login($member->id);
        if ($memberLoginResult['code'] != Code::SYSTEM_OK) {
            return $memberLoginResult;
        }

        //5 登录成功，返回结果
        $result["code"] = Code::SYSTEM_OK;
        $result["msg"]  = Msg::SYSTEM_OK;
        $result["data"] = $memberLoginResult['data'];
        return $result;
    }

    /**
     * 重置密码-token方式
     * @author Chengcheng
     * @date 2016-10-19 15:50:00
     * @param array $requestData
     * @return array
     */
    public static function resetPasswordByOld($requestData)
    {
        //0 预制返回结果
        $result         = array();
        $result["code"] = Code::SYSTEM_ERROR;
        $result["msg"]  = Msg::SYSTEM_ERROR;

        //1 验证旧密码是否正确
        $member = UserMember::find($requestData['member_id']);

        if (empty($member) || !password_verify($requestData['old_passwd'], $member->passwd)) {
            $result["code"] = Code::USER_PASSWORD_ERROR;
            $result["msg"]  = Msg::USER_PASSWORD_ERROR;
            return $result;
        }

        //2 修改密码
        $member->passwd = password_hash($requestData['new_passwd'], PASSWORD_DEFAULT);
        $member->save();

        //3 修改成功
        $result["code"] = Code::SYSTEM_OK;
        $result["msg"]  = Msg::SYSTEM_OK;
        return $result;
    }

    /**
     * 重置密码-code方式
     * @author Chengcheng
     * @date 2016-10-19 15:50:00
     * @param array $data
     * @return array
     */
    public static function resetPasswordByCode($data)
    {
        //0 预制返回结果
        $result         = array();
        $result["code"] = Code::SYSTEM_ERROR;
        $result["msg"]  = Msg::SYSTEM_ERROR;

        //3 判断验证码
        $emailCode = PublicToken::model()->where([['code', $data['code']], ['email', $data['email']]])->first();

        if (empty($emailCode) || strtotime($emailCode->available_time) < strtotime(Visitor::user()->time) || $emailCode->code != $data['code']) {
            $result["code"] = Code::USER_EMAIL_CODE_ERROR;
            $result["msg"]  = Msg::USER_EMAIL_CODE_ERROR;
            return $result;
        }

        //1 验证旧密码是否正确
        $member = UserMember::model()->where('email', $data['email'])->first();
        if (empty($member)) {
            $result["code"] = Code::USER_EMAIL_ERROR;
            $result["msg"]  = Msg::USER_EMAIL_ERROR;
            return $result;
        }

        //2 修改密码
        $member->passwd = password_hash($data['passwd'], PASSWORD_DEFAULT);
        $member->save();

        //3 修改成功
        $result["code"] = Code::SYSTEM_OK;
        $result["msg"]  = Msg::SYSTEM_OK;
        return $result;
    }

    /**
     * 用户注册
     * @author Chengcheng
     * @param array $data
     * @return array
     */
    public static function register($data)
    {
        //0 预制返回结果
        $result         = array();
        $result["code"] = Code::SYSTEM_ERROR;
        $result["msg"]  = Msg::SYSTEM_ERROR;
        $result["data"] = "";

        //1 判断邮箱是否存在
        $member = UserMember::model()->where('email', $data['email'])->first();
        if (!empty($member)) {
            $result["code"] = Code::USER_EMAIL_EXIT;     //手机号码不存在
            $result["msg"]  = Msg::USER_EMAIL_EXIT;
            return $result;
        }

        //2 判断手机是否存在
        if (!empty($data['phone'])) {
            $member = UserMember::model()->where('email', $data['phone'])->first();
            if (!empty($member)) {
                $result["code"] = Code::USER_MOBILE_EXIT;     //手机号码不存在
                $result["msg"]  = Msg::USER_MOBILE_EXIT;
                return $result;
            }
        }

        //3 判断验证码
        $emailCode = PublicToken::model()->where([['code', $data['code']], ['email', $data['email']]])->first();

        if (empty($emailCode) || strtotime($emailCode->available_time) < strtotime(Visitor::user()->time) || $emailCode->code != $data['code']) {
            $result["code"] = Code::USER_EMAIL_CODE_ERROR;
            $result["msg"]  = Msg::USER_EMAIL_CODE_ERROR;
            return $result;
        }

        //4 保存用户登录信息
        $member         = new UserMember();
        $member->email  = $data['email'];
        $member->mobile = $data['phone'];
        $member->name   = $data['name'];
        if (!empty($data['passwd'])) {
            $member->passwd = password_hash($data['passwd'], PASSWORD_DEFAULT);
        }
        //5 开始事务
        DB::beginTransaction();
        $member->save();
        //6 更新用户分组,默认配置 测试分组，ID为2， 请根据实际情况设置
        $member->role()->sync([2]);
        //7 提交事务
        DB::commit();

        //8 登录成功，返回结果
        $result["code"] = Code::SYSTEM_OK;
        $result["msg"]  = Msg::SYSTEM_OK;
        return $result;
    }

    /**
     * 微信授权登录
     * @author Chengcheng
     * @date   2016年10月23日 20:39:25
     * @param array $data
     * @return array
     */
    public static function wxLogin($data)
    {
        $result         = array();
        $result["code"] = Code::SYSTEM_ERROR;
        $result["msg"]  = Msg::SYSTEM_ERROR;

        //获取openId
        $auth = WeChat::user()->auth($data['code']);
        //var_dump($auth);
        //获取openId失败，系统错误，
        if (empty($auth['openid'])) {
            $result["code"] = Code::SYSTEM_ERROR;
            $result["msg"]  = "获取openid失败，请检查code是否正确";
            return $result;
        }
        $openId = $auth['openid'];
        $weInfo = WeChat::user()->getUserInfo($openId, $auth['access_token']);
        if (empty($weInfo)) {
            $result["code"] = Code::SYSTEM_ERROR;
            $result["msg"]  = "获取用户信息失败";
            return $result;
        }

        // 更新微信表信息
        $weData             = [];
        $weData['open_id']  = $weInfo["openid"];
        $weData['name']     = $weInfo["nickname"];
        $weData['gender']   = $weInfo["sex"];
        $weData['language'] = $weInfo["language"];
        $weData['city']     = $weInfo["city"];
        $weData['province'] = $weInfo["province"];
        $weData['country']  = $weInfo["country"];
        $weData['avatar']   = $weInfo["headimgurl"];
        //检查微信表用户信息是否存在
        $userWxCheck = WechatUser::model()->where('open_id', $openId)->first();
        if (!empty($userWxCheck)) {
            $weData['id']        = $userWxCheck->id;
            $weData['member_id'] = $userWxCheck->member_id;
        }
        $userWx = WechatUser::model()->edit($weData);

        // 更新用户表
        $userData             = [];
        $userData['open_id']  = $weInfo["openid"];
        $userData['name']     = $weInfo["nickname"];
        $userData['gender']   = $weInfo["sex"];
        $userData['language'] = $weInfo["language"];
        $userData['city']     = $weInfo["city"];
        $userData['province'] = $weInfo["province"];
        $userData['country']  = $weInfo["country"];
        $userData['avatar']   = $weInfo["headimgurl"];

        if (!empty($userWxCheck->member_id)) {
            $userData['id'] = $userWxCheck->member_id;
            $userData['roles'] = [2];
            $userMember     = UserMember::model()->edit($userData);
        } else {
            $userMember        = UserMember::model()->edit($userData);
            $userWx->member_id = $userMember->id;
            $userWx->save();
        }

        //登录
        $memberLoginResult = UserMember::model()->login($userMember->id);
        if ($memberLoginResult['code'] != Code::SYSTEM_OK) {
            return $memberLoginResult;
        }

        //登录成功，返回结果
        $result["code"] = Code::SYSTEM_OK;
        $result["msg"]  = Msg::SYSTEM_OK;
        $result["data"] = $memberLoginResult['data'];
        return $result;
    }

    /**
     * 小程序微信授权登录
     * @author Chengcheng
     * @date   2016年10月23日 20:39:25
     * @param array $data
     * @return array
     */
    public static function wxMiniLogin($data)
    {
        $result         = array();
        $result["code"] = Code::SYSTEM_ERROR;
        $result["msg"]  = Msg::SYSTEM_ERROR;
        $result["data"] = null;

        //获取openId
        $auth = WeChat::user()->auth($data['code']);

        $result["code"] = Code::SYSTEM_OK;
        $result["msg"]  = Msg::SYSTEM_OK;
        return $result;
    }
}
