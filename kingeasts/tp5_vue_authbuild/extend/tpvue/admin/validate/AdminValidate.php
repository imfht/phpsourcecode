<?php
// +----------------------------------------------------------------------
// | tp5_vue_authbuild
// +----------------------------------------------------------------------
// | Author: King east <1207877378@qq.com>
// +----------------------------------------------------------------------


namespace tpvue\admin\validate;


use think\Validate;

class AdminValidate extends Validate
{
    protected $rule = [
        'username|登陆账号'=>'require|max:30',
        'nickname|昵称'=>'require|max:30',
        'mobile|手机号'=>'mobile',
        'password|密码'=>'require|min:6|max:30',
        'repassword|确认密码'=>'confirm:password',
        'avatar|头像'=>'require|max:255',
        'status|状态'=>'require|number'
    ];

    public function sceneEdit()
    {
        return $this->remove([
            'password'=>'require',
            'repassword'=>'require'
        ]);
    }

}