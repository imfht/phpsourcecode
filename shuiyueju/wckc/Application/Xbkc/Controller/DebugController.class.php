<?php

namespace Home\Controller;
use Think\Page;


/**
 * 前台调试管理控制器
 * @author水月居 <singliang@163.com>
 */
class DebugController extends HomeController {

    /**
     * 用户找回密码
    */
    public function index(){
		echo "找回密码:";
		echo md5(sha1('123456') . C('DATA_AUTH_KEY'));
		
    }


}