<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
      $this->display();
    }

    public function captcha()
    {
        $builder = new \Gregwar\Captcha\CaptchaBuilder;;
        $builder->build();
        header('Content-type: image/jpeg');
        $builder->output();
    }
}