<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Common\Controller;
use Think\Controller;
/**
 * 后台公共控制器
 * @author
 */
abstract class CPublicController extends CommonController{
    /**
     * 登录公共方法
     * @return mixed
     */
    abstract public function login();

    /**
     * 注销共方法
     */
    abstract public function logout();

    /**
     * 图片验证码生成，用于登录和注册
     * @author jry <598821125@qq.com>
     */
    public function verify($vid = 1){
        $verify = new \Think\Verify();
        $verify->length = 4;
        $verify->entry($vid);
    }
    
    /**
     * 检测验证码
     * @param  integer $id 验证码ID
     * @return boolean 检测结果
     */
    function check_verify($code, $vid = 1){
        $verify = new \Think\Verify();
        return $verify->check($code, $vid);
    }
}
