<?php
namespace Home\Controller;
use Think\Controller;
class AppController extends AclController {
    public function index(){
       $this->display('index');
    }
}