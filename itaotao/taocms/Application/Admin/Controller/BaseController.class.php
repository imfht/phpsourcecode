<?php
/**
 * Backfrend Base Class 后台基类
 * Author: taotao
 * Date: 14-5-11
 * Time: 下午8:18
 */

namespace Admin\Controller;

use Think\Controller;

class BaseController extends Controller{

    /**
     * 后台控制器初始化
     */
    protected function _initialize(){
        if(!session("uid")){
            $this->redirect('Public/login');
        }
        if(session('uid')==1){
            return true;
        }
        $Auth  =   new \Think\Auth();
        if(!$Auth->check(MODULE_NAME.'-'.ACTION_NAME,session('uid'))){
            $this->error('你没有权限');
        }
    }
    public function menu(){

    }
}