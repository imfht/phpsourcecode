<?php
namespace addons\common\coupon\controller;

use think\addons\Controller;

class Index extends Controller
{
	public function _initialize(){
        $this->view->engine->layout(false);
        $this->view->replace([
                '__CSS__'       =>  '/addons/common/coupon/view/public/css',
                '__IMG__'       =>  '/addons/common/coupon/view/public/image',
            ]);
	}

    public function index()
    {

        return view('index_index');
    }





}
