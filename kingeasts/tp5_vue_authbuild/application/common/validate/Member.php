<?php
namespace app\common\validate;
use think\Validate;

class Member extends Validate
{

	protected $regex = [ 'mobile' => '/^1[3|4|5|7|8][0-9]\d{4,8}$/i'];

    protected $rule = [
		'mobile'  	=>  'require|number|length:11|regex:mobile',
		'password' 	=>  'require|length:6-18|confirm:repassword',
    ];

    protected $message = [
		'mobile.require'  		=>  '请输入用户手机号',
		'mobile.number'  		=>  '用户手机号必须为数字',
		'mobile.length'  		=>  '用户手机号不正确',
		'mobile.regex'     		=>  '请输入正确的手机号',
		'password.require' 		=>  '请输入6-16位密码！',
		'password.length'  		=>  '密码长度不符合!',
		'password.confirm' 	 	=>  '两次输入密码不一致!',
    ];

    public function isUserId($mobile,$rule,$data)
    {
        $is_res = db('member')->where(['id'=>$data['referee']])->field('id')->find();
        if (!$is_res) {
            return false;
        }
        return true;
    }

    protected $scene=[
        'addUser'  => ['mobile','password'],
        'editUser' => ['mobile'],
        'register' => ['mobile','password'],
        'setmobile' => ['mobile'],
    ];
}