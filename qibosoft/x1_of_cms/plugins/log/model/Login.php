<?php
namespace plugins\log\model;
use think\Model;


//城市地区
class Login extends Model
{
	
    // 设置当前模型对应的完整数据表名称
    protected $table = '__LOG_LOGIN__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = TRUE;
    
    /**
     * 登录日志
     * @param unknown $username
     * @param unknown $password
     * @return \plugins\log\model\Login
     */
    public static function login($username,$password)
    {
        $array = [
            'username'=>$username,
            'password'=>$password,
            'ip'=>get_ip(),
        ];
        return self::create($array);
    }
	
}