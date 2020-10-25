<?php

/**
 * WCMS 登陆器 只跟用户有关注册、登陆有关 其他无关 判断有无登陆 可以设置cookie
 * 描述 调用了MemberService指定接口  login  register getOneMemberByUsername
 * @author wolf
 * @since 2014-08-02 
 * @version 第4次简化
 *
 */
class AnonymousController extends Action
{
    /**
     * 管理员登录口
     */
    public function admin ()
    {
        $this->login();
    }

    /**
     * 用户注册 接口 调用用户服务
     * 只检测提交的字段是否合法
     * 
     * @todo 默认用户组未添加
     */
    public function register ()
    {
        $this->view()->display('file:anonymous/register.html');
    }

    /**
     * 用户提交注册
     */
    public function setRegister ()
    {
        $rs = self::getMemberService()->register($_POST);
        $this->sendNotice($rs['message'], null, $rs['status']);
    }

    public function password ()
    {
        $this->view()->display('file:anonymous/password.html');
    }

    public function mailPassword ()
    {
        $member = new MemberService();
        $member->mailPassword($_POST['mobile_phone']);
    }

    /**
     * 普通会员登录
     */
    public function login ()
    {
        $this->view()->display('file:anonymous/login.html');
    }



    /**
     * 登录验证
     */
    public function setLogin ()
    {
        $rs = self::getMemberService()->login($_POST);
       $this->sendNotice($rs['message'],$rs['data'],$rs['status']);
    }

    /**
     * 退出登录 同步登录
     */
    public function signout ()
    {
        setcookie("openid", "", - 86400, "/");
        $this->redirect("退出成功!", './index.php?anonymous/login');
    }

    /**
     * 获取用户服务类
     */
    public static function getMemberService ()
    {
        return new MemberService();
    }
}