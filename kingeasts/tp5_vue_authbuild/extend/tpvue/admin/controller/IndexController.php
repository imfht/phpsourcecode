<?php
namespace tpvue\admin\controller;


use tpvue\admin\App;
use tpvue\admin\traits\KeTemplateRender;

class IndexController extends BaseController
{
    use KeTemplateRender;

    protected $middleware = ['MemberLogin','Auth'];


    public function index(){
        return App::$view->fetch('index');
    }


    public function home(){
        return App::$view->fetch('home');
    }
}
