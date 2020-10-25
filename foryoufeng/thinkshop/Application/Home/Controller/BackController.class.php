<?php
namespace Home\Controller;
use Think\Controller;
class BackController extends Controller {
    public function index(){
        require(APP_PATH.'Admin/View/Public/login.html');
    }
}