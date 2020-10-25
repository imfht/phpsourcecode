<?php
namespace app\ucenter\widget;

use think\Controller;
use think\Db;
use app\ucenter\model\UcenterMember;

class Login extends Controller
{
    public function login($type = "quickLogin")
    {
        if ($type != "quickLogin") {
            if (is_login()) {
                redirect(Url('index/Index/index'));
            }
        }
        $this->assign('login_type', $type);
        $ph = [];
        check_login_type('username') && $ph[] = lang('_USERNAME_');
        check_login_type('email') && $ph[] = lang('_EMAIL_');
        check_login_type('mobile') && $ph[] = lang('_PHONE_');
        $this->assign('ph', implode('/', $ph));
        return $this->fetch('ucenter@widget/login');
    }

    public function doLogin()
    {
        $aUsername = $username = input('param.username','','text');
        $aPassword = input('param.password', '', 'text');
        $aVerify = input('param.verify', '', 'text');
        $aRemember = input('param.remember', 0, 'intval');//默认记住登录 0：不记住；1：记住

        if(empty($aUsername)) $this->error(lang('_MI_USERNAME_'));
        if(empty($aPassword)) $this->error(lang('_PW_INPUT_ERROR_'));
        /* 检测验证码 */
        
        if (check_verify_open('login')) {
            if (!check_verify($aVerify,1)) {

                $res['code']=0;
                $res['msg']=lang('_INFO_VERIFY_CODE_INPUT_ERROR_').lang('_PERIOD_');
                return $res;
            }
        }

        /* 根据type或用户名来判断注册使用的是用户名、邮箱或者手机 */
        check_username($aUsername, $email, $mobile, $aUnType);
        //echo $aUnType;exit;
        if (!check_reg_type($aUnType)) {
            $res['msg']=lang('_INFO_TYPE_NOT_OPENED_').lang('_PERIOD_');
        }
        //用户登录验证
        $uid = model('ucenter/UcenterMember')->login($username, $aPassword, $aUnType);

        if (0 < $uid) { //登录成功
            
            $Member = model('Member');
            $args['uid'] = $uid;
            $args = array('uid'=>$uid,'nickname'=>$username);
            check_and_add($args);
            //登陆用户记录session
            if ($Member->login($uid, $aRemember == 1)) { //登录用户
                //TODO:跳转到登录前页面
                
                $res['code']=1;
                $res['msg']=lang('_WELCOME_RETURN_');
                $res['uid']=$uid;
            } else {
                $res['code']=0;
                $res['msg']=$Member->getError();
            }

        } else { //登录失败
            switch ($uid) {
                case -1:
                    $res['code']=0;
                    $res['msg']= lang('_INFO_USER_FORBIDDEN_');
                    break; //系统级别禁用
                case -2:
                    $res['code']=0;
                    $res['msg']= lang('_INFO_PW_ERROR_').lang('_EXCLAMATION_');
                    break;
                default:
                    $res['code']=0;
                    $res['msg']= $uid;
                    break; // 0-接口参数错误（调试阶段使用）
            }
        }
        return $res;
    }
} 