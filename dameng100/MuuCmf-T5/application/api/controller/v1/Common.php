<?php
namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\api\controller\Api;
use think\Response;
use think\Db;
use app\api\controller\UnauthorizedException;
use app\api\controller\v1\Base;

/**
 * 所有资源类接都必须继承基类控制器
 * 基类控制器提供了基础的验证，包含app_token,请求时间，请求是否合法的一系列的验证
 * 在所有子类中可以调用$this->clientInfo对象访问请求客户端信息，返回为一个数组
 * 在具体资源方法中，不需要再依赖注入，直接调用$this->request返回为请具体信息的一个对象
 */

/**
 * 公共常用功能接口
 */
class Common extends Base
{   
    /**
     * 允许访问的方式列表，资源数组如果没有对应的方式列表，请不要把该方法写上，如user这个资源，客户端没有delete操作
     */
    public $restMethodList = 'get|post|put';
    public $apiAuth = false;

    
    /**
     * restful没有任何参数
     *
     * @return \think\Response
     */
    public function index()
    {
        return $this->sendError('uid参数错误');  
    }

    /**
     * post方式
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save()
    {
        //根据action 参数判断操作类型
        $action = input('action','','text');
        switch($action){
            case 'send_verify'://发送验证码
                
                $uid = input('uid',get_uid(),'intval');
                $aAccount = $cUsername = input('post.account', '', 'text');
                $aType = input('post.type', '', 'text');
                $aType = $aType == 'mobile' ? 'mobile' : 'email';
                $aDriver = input('post.driver','other','text');
                $sendType = input('post.sendtype','verify','text'); //发送的短信类型，如：验证码类、通知类，推广类

                if (!check_reg_type($aType)) {
                    $str = $aType == 'mobile' ? lang('_PHONE_') : lang('_EMAIL_');
                    return $this->sendError($str . lang('_ERROR_OPTIONS_CLOSED_').lang('_EXCLAMATION_'));
                }

                if (empty($aAccount)) { 
                    return $this->sendError(lang('_ERROR_ACCOUNT_CANNOT_EMPTY_'));  
                }

                check_username($cUsername, $cEmail, $cMobile);
                $time = time();

                if($aType == 'mobile'){
                    $resend_time =  modC('SMS_RESEND','60','USERCONFIG');
                    if($time <= session('verify_time')+$resend_time ){
                        $result = lang('_ERROR_WAIT_1_').($resend_time-($time-session('verify_time'))).lang('_ERROR_WAIT_2_');
                        return $this->sendError($result);
                    }
                }

                if ($aType == 'email' && empty($cEmail)) {

                    return $this->sendError(lang('_ERROR__EMAIL_'));  
                }

                if ($aType == 'mobile' && empty($cMobile)) {

                    return $this->sendError(lang('_ERROR_PHONE_'));  
                }

                //判断是否是已存在用户，由于部分操作需要向存在的用户发送验证，在这里做判断
                if($aDriver==='edit' || $aDriver==='config'){
                    $checkIsExist = Db::name('UcenterMember')->where([$aType => $aAccount])->find();
                    if (!$checkIsExist) {
                        $str = $aType == 'mobile' ? lang('_PHONE_') : lang('_EMAIL_');
                        return $this->sendError(lang('_ERROR_USED_1_') . $str . lang('_ERROR_USED_3_').lang('_EXCLAMATION_'));//还未注册的数据返回错误
                    }
                }

                $verify = model('Verify')->addVerify($aAccount, $aType, $uid);
                if (!$verify) {
                    $result = lang('_ERROR_FAIL_SEND_').lang('_EXCLAMATION_');
                    return $this->sendError($result);
                }

                switch ($aType) {
                    case 'mobile':
                        //发送手机短信验证
                        $content = modC('SMS_CONTENT', '{$verify}', 'USERCONFIG');
                        $content = str_replace('{$verify}', $verify, $content);
                        $content = str_replace('{$account}', $aAccount, $content);

                        //发送类型，暂只处理验证类
                        if($sendType == 'verify'){
                            $param = [
                                'code'=>$verify,
                            ];
                            $param = json_encode($param);
                        }
                        //TODO:其它类型该版本暂不写，这里留个记
                        
                        $res = sendSMS($aAccount, $content, $sendType, $param);
                        break;
                    case 'email':
                        //发送验证邮箱
                        $content = modC('REG_EMAIL_VERIFY', '{$verify}', 'USERCONFIG');
                        $content = str_replace('{$verify}', $verify, $content);
                        $content = str_replace('{$account}', $aAccount, $content);
                        $res = send_mail($aAccount, modC('WEB_SITE_NAME', lang('_MUUCMF_'), 'Config') . lang('_EMAIL_VERIFY_2_'), $content);
                        
                        break;
                }

                if ($res === true) {
                    if($aType == 'mobile'){
                        session('verify_time',$time);
                    }
                    return $this->sendSuccess('发送成功');
                    
                } else {

                    return $this->sendError($res);  
                }
            break;
            
        }

        return $this->sendError('无操作参数');  
    }

    /**
     * get方式
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {   
        
    }

}
