<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-11
 * Time: PM3:40
 */

namespace Ucenter\Controller;

use Think\Controller;

class VerifyController extends Controller
{
    /**
     * sendVerify 发送验证码
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function sendVerify()
    {
        $aAccount = $cUsername = I('post.account', '', 'op_t');
        $aType = I('post.type', '', 'op_t');
        $aType = $aType == 'mobile' ? 'mobile' : 'email';
        $aAction = I('post.action', 'config', 'op_t');//member或config或find:找回密码操作
        if (!check_reg_type($aType)) {
            $str = $aType == 'mobile' ? L('_PHONE_') : L('_EMAIL_');
            $this->error($str . L('_ERROR_OPTIONS_CLOSED_').L('_EXCLAMATION_'));
        }


        if (empty($aAccount)) {
            $this->error(L('_ERROR_ACCOUNT_CANNOT_EMPTY_'));
        }
        check_username($cUsername, $cEmail, $cMobile);
        $time = time();
        if($aType == 'mobile'){
            $resend_time =  modC('SMS_RESEND','60','USERCONFIG');
            if($time <= session('verify_time')+$resend_time ){
                $this->error(L('_ERROR_WAIT_1_').($resend_time-($time-session('verify_time'))).L('_ERROR_WAIT_2_'));
            }
        }


        if ($aType == 'email' && empty($cEmail)) {
            $this->error(L('_ERROR__EMAIL_'));
        }
        if ($aType == 'mobile' && empty($cMobile)) {
            $this->error(L('_ERROR_PHONE_'));
        }
        
        $checkIsExist = UCenterMember()->where(array($aType => $aAccount))->find();
        //判断是否是已存在用户，由于部分操作需要向存在的用户发送验证，在这里做判断
        if($aAction==='find'){
            if (!$checkIsExist) {
                $str = $aType == 'mobile' ? L('_PHONE_') : L('_EMAIL_');
                $this->error(L('_ERROR_USED_1_') . $str . L('_ERROR_USED_3_').L('_EXCLAMATION_'));//还未注册的数据返回错误
            }
        }else{
            if ($checkIsExist) {
                $str = $aType == 'mobile' ? L('_PHONE_') : L('_EMAIL_');
                $this->error(L('_ERROR_USED_1_') . $str . L('_ERROR_USED_2_').L('_EXCLAMATION_'));//已被占用的数据返回错误
            }
        }

        $verify = D('Verify')->addVerify($aAccount, $aType);
        if (!$verify) {
            $this->error(L('_ERROR_FAIL_SEND_').L('_EXCLAMATION_'));
        }
        if($aAction==='find'){
            $res = doSendVerify($aAccount, $verify, $aType);
        }else{
            $res = doSendVerify($aAccount, $verify, $aType);
        }
        
        if ($res === true) {
            if($aType == 'mobile'){
                session('verify_time',$time);
            }
            $this->success(L('_ERROR_SUCCESS_SEND_'));
        } else {
            $this->error($res);
        }

    }


}