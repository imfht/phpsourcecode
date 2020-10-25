<?php
namespace app\home\controller;

use app\common\controller\Home;

class Link extends Home
{
    //初始化 需要调父级方法
    public function initialize()
    {        
        call_user_func(['parent', __FUNCTION__]); 
    }
}
