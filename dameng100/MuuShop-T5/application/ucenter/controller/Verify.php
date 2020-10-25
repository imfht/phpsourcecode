<?php
namespace app\ucenter\controller;

use think\Controller;
use think\Db;

class Verify extends Controller
{
    /**
     * sendVerify 发送验证码
     */
    public function sendVerify()
    {
        $aAccount = $cUsername = input('post.account', '', 'text');
        $aType = input('post.type', '', 'text');
        $aType = $aType == 'mobile' ? 'mobile' : 'email';
        $aAction = input('post.action', 'config', 'text');//member或config或find:找回密码操作
        if (!check_reg_type($aType)) {
            $str = $aType == 'mobile' ? lang('_PHONE_') : lang('_EMAIL_');
            $this->error($str . lang('_ERROR_OPTIONS_CLOSED_').lang('_EXCLAMATION_'));
        }

        if (empty($aAccount)) {
            $this->error(lang('_ERROR_ACCOUNT_CANNOT_EMPTY_'));
        }
        check_username($cUsername, $cEmail, $cMobile);
        $time = time();
        if($aType == 'mobile'){
            //短信验证码的有效期，默认60秒
            $resend_time =  modC('SMS_RESEND','60','USERCONFIG');
            if($time <= session('verify_time')+$resend_time ){
                $this->error(lang('_ERROR_WAIT_1_').($resend_time-($time-session('verify_time'))).lang('_ERROR_WAIT_2_'));
            }
        }

        if ($aType == 'email' && empty($cEmail)) {
            $this->error(lang('_ERROR__EMAIL_'));
        }
        if ($aType == 'mobile' && empty($cMobile)) {
            $this->error(lang('_ERROR_PHONE_'));
        }
        
        $checkIsExist = Db::name('UcenterMember')->where([$aType => $aAccount])->find();
        //判断是否是已存在用户，由于部分操作需要向存在的用户发送验证，在这里做判断
        if($aAction==='find'){
            if (!$checkIsExist) {
                $str = $aType == 'mobile' ? lang('_PHONE_') : lang('_EMAIL_');
                $this->error(lang('_ERROR_USED_1_') . $str . lang('_ERROR_USED_3_').lang('_EXCLAMATION_'));//还未注册的数据返回错误
            }
        }else{
            if ($checkIsExist) {
                $str = $aType == 'mobile' ? lang('_PHONE_') : lang('_EMAIL_');
                $this->error(lang('_ERROR_USED_1_') . $str . lang('_ERROR_USED_2_').lang('_EXCLAMATION_'));//已被占用的数据返回错误
            }
        }

        $verify = model('Verify')->addVerify($aAccount, $aType);
        if (!$verify) {
            $this->error(lang('_ERROR_FAIL_SEND_').lang('_EXCLAMATION_'));
        }
        if($aAction==='find'){//找回密码
            $res =  doSendVerify($aAccount, $verify, $aType);
        }
        if($aAction==='member'){//注册会员
            $res =  doSendVerify($aAccount, $verify, $aType);
        }
        
        if ($res === true) {
            if($aType == 'mobile'){
                session('verify_time',$time);
            }
            $this->success(lang('_ERROR_SUCCESS_SEND_'));
        } else {
            $this->error($res);
        }

    }
}