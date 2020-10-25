<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2016 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace app\ucenter\widget;

use app\ucenter\logic\UcenterMember;
use app\common\model\Member;

class Login
{
    public function login($type = "quickLogin")
    {
        if ($type != "quickLogin") {
            if (is_login()) {
                redirect(url('Home/Index/index'));
            }
        }
        $this->assign('login_type', $type);
        $ph = array();
        check_login_type('username') && $ph[] = lang('_USERNAME_');
        check_login_type('email') && $ph[] = lang('_EMAIL_');
        check_login_type('mobile') && $ph[] = lang('_PHONE_');
        $this->assign('ph', implode('/', $ph));
        $this->display('Widget/Login/login');
    }

    public function doLogin()
    {
        $aUsername = $username = input('post.username', '', 'op_t');
        $aPassword = input('post.password', '', 'op_t');
        $aVerify = input('post.verify', '', 'op_t');
        $aRemember = input('post.remember', 0, 'intval');

        /* 检测验证码 */
    //    if (check_verify_open('login')) {
    //        if (!check_verify($aVerify)) {
    //            $res['info']=lang('_INFO_VERIFY_CODE_INPUT_ERROR_').lang('_PERIOD_');
    //            return $res;
    //        }
    //    }

        /* 调用UC登录接口登录 */
    //    check_username($aUsername, $email, $mobile, $aUnType);

    //    if (!check_reg_type($aUnType)) {
    //        $res['info']=lang('_INFO_TYPE_NOT_OPENED_').lang('_PERIOD_');
    //    }
        /* 系统级用户登录 UcenterMember */
        $User = new UcenterMember();
        $uid = $User->login($username, $aPassword, 3);  //前台只能用手机号码登录，即登录方式为3

        if (0 < $uid) { //UC登录成功
            /* 登录用户 */
            $Member = new Member();
            $args['uid'] = $uid;
            $args = array('uid'=>$uid,'nickname'=>$username);
           // check_and_add($args);
            if ($Member->login($uid, $aRemember == 1)) { //登录用户
                //TODO:跳转到登录前页面

                $res['status']=1;
                //$this->success($html, get_nav_url(C('AFTER_LOGIN_JUMP_URL')));
            } else {
                $res['status']=0;
                $res['info']=$Member->getError();
            }

        } else { //登录失败
            switch ($uid) {
                case -1:
                    $res['info']= lang('_INFO_USER_FORBIDDEN_');
                    break; //系统级别禁用
                case -2:
                    $res['info']= lang('_INFO_PW_ERROR_').lang('_EXCLAMATION_');
                    break;
                default:
                    $res['info']= $uid;
                    break; // 0-接口参数错误（调试阶段使用）
            }
        }
        return $res;
    }
} 