<?php

namespace app\admin\controller;
use think\Request;

/**
 * Description of test
 * 测试文件
 * @author static7
 */
class Test extends Admin {

    /**
     * 测试
     * @author staitc7 <static7@qq.com>
     */
    public function index() {
        $this->view->metaTitle = '测试';
        return $this->view->fetch(); 
    }
    /**
     * 方法名称或者用途
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     * @return mixed   
     */
    public function test(Request $Requset) {
        dump($Requset->module());
    }

}
