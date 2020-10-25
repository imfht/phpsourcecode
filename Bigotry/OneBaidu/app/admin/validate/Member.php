<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\validate;

use app\admin\logic\Member as LogicMember;

/**
 * 会员验证器
 */
class Member extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        
        'username'  => 'require|unique:member',
        'password'  => 'require|confirm|length:6,20',
        'email'     => 'require|email|unique:member',
        'nickname'  => 'require',
        'mobile'    => 'unique:member',
    ];
    
    // 验证提示
    protected $message  =   [
        
        'username.require'    => '用户名不能为空',
        'username.unique'     => '用户名已存在',
        'nickname.require'    => '昵称不能为空',
        'password.require'    => '密码不能为空',
        'password.confirm'    => '两次密码不一致',
        'password.length'     => '密码长度为6-20字符',
        'email.require'       => '邮箱不能为空',
        'email.email'         => '邮箱格式不正确',
        'email.unique'        => '邮箱已存在',
        'mobile.unique'       => '手机号已存在'
    ];

    // 应用场景
    protected $scene = [
        
        'add'   =>  ['username','password','email'],
        'edit'  =>  ['username','nickname','email','mobile'],
    ];
    
    // 扩展验证规则 验证会员登录信息
    public function checkLoginData($data)
    {
        
        if(empty($data['username']))                                : $this->error = '账号不能为空';    return false; endif;
        if(empty($data['password']))                                : $this->error = '密码不能为空';    return false; endif;
        if(empty($data['verify']))                                  : $this->error = '验证码不能为空';  return false; endif;
        if(!captcha_check($data['verify']))                         : $this->error = '验证码输入错误';  return false; endif;
        
        $memberLogic = get_sington_object('memberLogic', LogicMember::class);
        
        $member = $memberLogic->getMemberInfo(['username' => $data['username']]);
        
        if(empty($member['id']))                                    : $this->error = '用户账号不存在';  return false; endif;
        if(data_md5_key($data['password']) != $member['password'])  : $this->error = '密码输入错误';    return false; endif;
        
        return $member;
    }
}
