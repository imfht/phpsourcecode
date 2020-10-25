<?php
namespace Control\Widget;
use Think\Controller;
class PublicWidget extends Controller {
    public function index($name){
        $this->theme('admin')->display("Public:$name");
    }
}