<?php
namespace app\app\controller;
use think\Controller;
class BaseController extends Controller
{
    public function _initialize(){
        //资源加载
    	if(strstr(request()->baseFile(), 'public')){
            $this->view->replace([
                '__PUBLIC__'       =>  request()->root(true),
            ]);
        }else{
            $this->view->replace([
                '__PUBLIC__'       =>  request()->root(true).'/public',
            ]);
        }
        
        $this->view->engine->layout(false);
    }




}