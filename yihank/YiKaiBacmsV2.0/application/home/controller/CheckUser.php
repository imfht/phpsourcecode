<?php
namespace app\home\controller;
class CheckUser extends Site{
    public function __construct(\think\Request $request){
        parent::__construct($request);
        /* 设置路由参数 */
    }
    //当任何函数加载时候  会调用此函数
    public function _initialize(){//默认的方法  会自动执行 特征有点像构造方法
        parent::_initialize();
        $user_id=session('home_user.user_id');
        if (empty($user_id)){
            return $this->error('请您登录',url('index/login'));
        }
        $user_info=model('User')->getInfo($user_id);
        $this->assign('user_info',$user_info);
    }
}
