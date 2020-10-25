<?php
// 会员模型       
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
namespace tpvue\admin\model;


use tpvue\admin\hooks\admin\LoginHook;
use think\facade\Hook;

class AdminModel extends BaseModel
{
    protected $updateTime = 'update_time';

    // 自动完成
    protected $auto       = ['login_ip'];
    protected $insert     = ['username', 'nickname', 'register_ip', 'login_time', 'status' => 1];

    public static function init()
    {
        self::beforeInsert(function ($row) {
            $row->register_time = $_SERVER['REQUEST_TIME'];
        });
    }

    protected function setNumberAttr($value)
    {
        if ($value) {
            return build_user_no(5, $value);
        } else {
            return build_user_no(10);
        }
    }

    protected function setUsernameAttr($value)
    {
        if ($value) {
            return $value;
        }

        return 'app_'.mt_rand(1000,9999);
    }

    protected function setPasswordAttr($value)
    {
        if(!empty($value)){
            return md5($value);
        }else{
            return '';
        }
    }

    protected function setNicknameAttr($value)
    {
        if ($value) {
            return $value;
        }

        return 'nickName_'.mt_rand(100,9999);
    }

    protected function setRegisterIpAttr()
    {
        return request()->ip();
    }

    protected function setLoginIpAttr()
    {
        return request()->ip();
    }

    protected function setLoginTimeAttr()
    {
        return time();
    }
    
    /**
     * [login 后端用户登录认证]
     * @param  [string]  $loginId  [登录ID]
     * @param  [string]  $password [用户密码]
     * @param  [int]     $type     [用户名类型 （1-用户编号, 2-用户账户, 3-手机, 4-用户昵称, 5-用户邮件, 6-全部）]
     * @return [int]               [登录成功-用户ID，登录失败-错误编号]
     */
    public static function login($loginId, $password, $type = 6, $source = "pc"){
        switch ($type) {
            case 2:
                $sqlmap = 'username';
                break;
            case 3:
                $sqlmap = 'mobile';
                break;
            case 4:
                $sqlmap = 'nickname';
                break;
            case 6:
                $sqlmap = 'username|mobile|nickname';
                break;
            default:
                return 0; //参数错误
        }
        /* 获取用户数据 */
        //$member = Db('member')->whereOr($sqlmap, $loginId)->find();
        $member = self::where($sqlmap, $loginId)->find();

        if($member) {
            if (!$member->status) {
                return -2; //用户被禁用
            }
            /* 验证用户密码 */
            if(md5($password) === $member->password){

                // 触发登录成功钩子
                Hook::exec([LoginHook::class, 'success'], [$member, $source]);

                return $member->id; //登录成功，返回用户ID
            } else {
                return -3; //密码错误
            }
        } else {
            return -1; //用户不存在
        }

        return true;
    }


    public function groups()
    {
        return $this->hasMany('AdminAuthGroup', 'uid', 'id');
    }

}