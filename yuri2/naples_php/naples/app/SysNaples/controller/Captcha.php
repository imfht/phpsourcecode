<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/12/6
 * Time: 10:34
 */

namespace naples\app\SysNaples\controller;


use naples\lib\base\Controller;
use naples\lib\Factory;

class Captcha extends Controller
{
    function index(){
        config('debug',false);
        $width=empty($_GET['width'])?0:$_GET['width'];
        $height=empty($_GET['height'])?0:$_GET['height'];
        $captcha=Factory::getCaptcha();
        $captcha->setSize($width,$height)->doImg();
        session('sysNaples.captchaCode',$captcha->getCode());
    }
}