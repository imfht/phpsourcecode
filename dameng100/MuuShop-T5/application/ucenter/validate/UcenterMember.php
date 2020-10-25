<?php
namespace app\ucenter\validate;

use think\Validate;
use think\Db;

class UcenterMember extends Validate
{
    //需要验证的键值
    protected $rule =   [
        'email'              => "email|checkDenyEmail|unique:ucenter_member",  //验证邮箱|验证邮箱在表中唯一性
        'username'           => "checkUsername|checkUsernameLength|checkDenyMember|unique:ucenter_member",
        'mobile'             => "regex:/^(1[3|4|5|8])[0-9]{9}$/|checkDenyMobile|unique:ucenter_member",
        'password'           => 'require|length:6,30',
        'confirm_password'   => 'require|length:6,30|confirm:password',
    ];

    //验证不符返回msg
    protected $message  =   [
        'email.email'               => -5,
        'email.unique'              => -8,
        'username.unique'           => -3,//'用户名已存在',
        'mobile.unique'             => -11,
        'mobile.regex'              => -9,//手机格式错误
        'password.require'          => -4,
        'password.length'           => -4,
        'confirm_password.require'  => -41,
        'confirm_password.length'   => -42,
        'confirm_password.confirm'  => -43,  
    ];
    //验证场景
    protected $scene = [
        'password'  =>  ['password','confirm_password'],
        'reg'  =>  ['username','email','password'],
    ];

    // 自定义验证规则
    /**
     * 验证用户名长度
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    protected function checkUsernameLength($value)
    {
        $length = mb_strlen($value, 'utf-8'); // 当前数据长度
        if ($length < modC('USERNAME_MIN_LENGTH',2,'USERCONFIG') || $length > modC('USERNAME_MAX_LENGTH',32,'USERCONFIG')) {
            return -1;
        }
        return true;
    }
    /**
     * 检查用户名格式
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    protected function checkUsername($value)
    {

        //如果用户名中有空格，不允许注册
        if (strpos($value, ' ') !== false) {
            return false;
        }
        preg_match("/^[a-zA-Z0-9_]{0,64}$/", $value, $result);

        if (!$result) {
            return -12;
        }
        return true;
    }

    /**
     * 检测用户名是不是被禁止注册(保留用户名)
     * @param  string $username 用户名
     * @return boolean          ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyMember($value)
    {
        $denyName=Db::name("Config")->where(['name' => 'USER_NAME_BAOLIU'])->value('value');
        if($denyName!=''){
            $denyName=explode(',',$denyName);
            foreach($denyName as $val){
                if(!is_bool(strpos($value,$val))){
                    return -2;
                }
            }
        }
        return true;
    }
    /**
     * 检测邮箱是不是被禁止注册
     * @param  string $email 邮箱
     * @return boolean       ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyEmail($value)
    {
        return true; //TODO: 暂不限制，下一个版本完善
    }

    /**
     * 检测手机是不是被禁止注册
     * @param  string $mobile 手机
     * @return boolean        ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyMobile($value)
    {
        return true; //TODO: 暂不限制，下一个版本完善
    }

 }   