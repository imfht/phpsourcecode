<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 1/15/14
 * Time: 4:17 PM
 */

namespace Api\Controller;
//use Addons\ResetByEmail\ResetByEmailAddon;
use Think\Controller;
use User\Api\UserApi;
use Addons\Tianyi\TianyiAddon;

class PublicController extends ApiController {
    public function login($username, $password) {
        //登录单点登录系统
        $result = $this->api->login($username, $password, 1); //1表示登录类型，使用用户名登录。
        if($result <= 0) {
            $message = $this->getLoginErrorMessage($result);
            $code = $this->getLoginErrorCode($result);
            $this->apiError($code,$message);
        } else {
            $uid = $result;
        }
        //登录前台
        $model = D('Home/Member');
        $result = $model->login($uid);
        if(!$result) {
            $message = $model->getError();
            $this->apiError(604,$message);
        }
        //返回成功信息
        $extra = array();
        $extra['session_id'] = session_id();
        $extra['uid'] = $uid;
        $this->apiSuccess("登录成功", null, $extra);
    }

    public function logout() {
        $this->requireLogin();
        //调用用户中心
        $model = D('Home/Member');
        $model->logout();
        session_destroy();
        //返回成功信息
        $this->apiSuccess("登出成功");
    }

    public function register($username, $password) {
        //调用用户中心
        $api = new UserApi();
        $uid = $api->register($username, $password, $username.'@username.com'); // 邮箱为空
        if($uid <= 0) {
            $message = $this->getRegisterErrorMessage($uid);
            $code = $this->getRegisterErrorCode($uid);
            $this->apiError($code,$message);
        }
        //返回成功信息
        $extra = array();
        $extra['uid'] = $uid;
        $this->apiSuccess("注册成功", null, $extra);
    }

    public function sendSms($mobile=null) {
        //如果没有填写手机号码，则默认使用已经绑定的手机号码
        if($mobile==='')
        {
            $this->apiError(802, "请输入手机号码。");
        }
        $uid = $this->getUid();
        $user = $this->getCombinedUser($uid);
        if($mobile === null) {
            $this->requireLogin();
            $mobile = $user['mobile'];
        }
        if(!$mobile) {
            $this->apiError(801, "用户未绑定手机号");
        }
        //调用短信插件发送短信
        $tianyi = new TianyiAddon;
        $result = $tianyi->sendVerify($mobile);
        if($result < 0) {
            $this->apiError(802, "短信发送失败：".$tianyi->getError());
        }
        //将手机号保存在session中
        saveMobileInSession($mobile);
        //显示成功消息
        $result = array('session_id'=>session_id());
        $this->apiSuccess("短信发送成功", null, $result);
    }

    public function resetPassword($verify, $new_password) {
        //检验校验码是否正确
        $mobile = getMobileFromSession();
        if(!$mobile) {
            $this->apiError(903, "未发送短信验证码");
        }
        $tianyi = new TianyiAddon;
        if(!$tianyi->checkVerify($mobile, $verify)) {
            $this->apiError(803, "校验码错误");
        }
        //根据手机号查询UID
        $uid = $this->api->getUidByMobile($mobile);
        if(!$uid) {
            $this->apiError(902, "该手机尚未绑定任何帐号");
        }
        //设置新密码
        $result = $this->updateUser($uid, array('password'=>$new_password));
        if(!$result) {
            $this->apiError(901, "更新用户信息失败：".$this->api->getError());
        }
        // TODO: 清除已登录的SESSION，强制重新登录
        //返回成功信息
        $this->apiSuccess("密码修改成功");
    }

    public function resetPasswordByEmail($email) {
        //调用找回密码组件
        $addon = new ResetByEmailAddon();
        $result = $addon->sendEmail($email);
        if(!$result) {
            $this->apiError(0,$addon->getError());
        }
        //返回结果
        $this->apiSuccess('邮件发送成功，请登录自己的邮箱找回密码');
    }
}