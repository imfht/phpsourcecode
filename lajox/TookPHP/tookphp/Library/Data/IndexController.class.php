<?php
//测试控制器类
namespace Home\Controller;
class IndexController extends \Took\Controller{
    //动作方法
    public function index(){
        //显示视图
        $this->display();
    }
}
