<?php
namespace app\home\controller;

use app\common\controller\Home;

class User extends Home
{
    //初始化 需要调父级方法
    public function initialize()
    {
        $this->Auth->allow(['login']);
        call_user_func(['parent', __FUNCTION__]); 
    }
    
    public function login()
    {
        $this->redirect('manage/User/login');
    }
}
