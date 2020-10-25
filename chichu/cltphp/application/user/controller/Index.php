<?php
namespace app\user\controller;
use think\Input;
class Index extends Common{
    public function initialize(){
        parent::initialize();

    }
    public function index(){
        $this->assign('title','会员中心');
        return $this->fetch();
    }
}