<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-11
 * Time: PM1:13
 */

namespace app\ucenter\controller;

use think\Controller;

class System extends Base
{
    public function _initialize(){
        parent::_initialize();

    }
    public function index($uid = null)
    {
        //调用API获取基本信息
        $user = query_user(array('nickname', 'email', 'mobile', 'last_login_time', 'last_login_ip', 'score', 'reg_time', 'title', 'avatar256','rank_link'), $uid);
        //显示页面
        $this->defaultTabHash('index');
        $this->assign('user', $user);
        $this->assign('call', $this->getCall($uid));
        $this->display('basic');
    }


    public function logout()
    {
        //调用退出登录的API
        D('Member')->logout();
        $html='';
        if(UC_SYNC && is_login() != 1){
            include_once './api/uc_client/client.php';
            $html = uc_user_synlogout();
        }

        $oc_config =  include_once './OcApi/oc_config.php';
        if ($oc_config['SSO_SWITCH']) {
            include_once  './OcApi/OCenter/OCenter.php';
            $OCApi = new \OCApi();
            $html = $OCApi->ocSynLogout();
        }

        exit(json_encode(array('message' =>lang('_SUCCESS_LOGOUT_').lang('_PERIOD_'),'url' => Url('Home/Index/index'),'html'=>$html)));
        //显示页面
        //$this->success($result['message'], Url('Home/Index/index'));
    }

    public function changePassword()
    {
        $this->defaultTabHash('change-password');
        return $this->fetch();
    }


    public function changeSignature()
    {
        $this->defaultTabHash('change-signature');
        return $this->fetch();
    }

    public function doChangeSignature($signature)
    {
        //调用接口
        $result = callApi('User/setProfile', array('signature' => $signature));
        $this->ensureApiSuccess($result);

        //显示成功信息
        $this->success($result['message']);
    }

    public function changeEmail()
    {
        $this->defaultTabHash('change-email');
        return $this->fetch();
    }

    public function doChangeEmail($email)
    {
        //调用API
        $result = callApi('User/setProfile', array(null, $email));
        $this->ensureApiSuccess($result);

        //显示成功信息
        $this->success($result['message']);
    }

    public function unbindMobile()
    {
        //确认用户已经绑定手机
        $profile = callApi('User/getProfile');
        if (!$profile['mobile']) {
            $this->error(lang('_ERROR_PHONE_NOT_BIND_'), Url('ucenter/Index/index'));
        }

        //发送验证码到已经绑定的手机上
        $result = callApi('Public/sendSms');
        $this->ensureApiSuccess($result);

        //显示页面
        $this->defaultTabHash('index');
        return $this->fetch();
    }

    public function doUnbindMobile($verify)
    {
        //调用解绑手机的API
        $result = callApi('User/unbindMobile', array($verify));
        if (!$result['success']) {
            $this->error($result['message']);
        }
        //显示成功消息
        $this->success($result['message'], Url('ucenter/Index/index'));
    }

    public function bindMobile()
    {
        //显示页面
        $this->defaultTabHash('index');
        return $this->fetch();
    }

    public function doBindMobile($mobile)
    {
        //调用API发送手机验证码
        $result = callApi('Public/sendSms', array($mobile));
        $this->ensureApiSuccess($result);

        //显示成功消息
        $this->success($result['message']);
    }

    public function doBindMobile2($verify)
    {
        //调用API绑定手机
        $result = callApi('User/bindMobile', array($verify));
        $this->ensureApiSuccess($result);

        //显示成功消息
        $this->success($result['message'], Url('ucenter/Index/index'));
    }

    public function fans($page = 1)
    {

        $this->assign('tab', 'fans');
        $fans = model('Follow')->getFans(is_login(), $page, array('avatar128', 'id', 'nickname', 'fans', 'following', 'weibocount', 'space_url','title'),$totalCount);
        $this->assign('fans', $fans);
        $this->assign('totalCount',$totalCount);
        return $this->fetch();
    }

    public function following($page=1)
    {
        $following = model('Follow')->getFollowing(is_login(), $page, array('avatar128', 'id', 'nickname', 'fans', 'following', 'weibocount', 'space_url','title'),$totalCount);
        $this->assign('following',$following);
        $this->assign('totalCount',$totalCount);
        $this->assign('tab', 'following');
        return $this->fetch();
    }
}