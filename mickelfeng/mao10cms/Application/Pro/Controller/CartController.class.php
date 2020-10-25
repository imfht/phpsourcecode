<?php
namespace Pro\Controller;
use Think\Controller;
class CartController extends Controller {
    public function index(){
        $this->page = M('action')->where("user_id='".mc_user_id()."' AND action_key='cart'")->order('id desc')->select();
        $this->theme(mc_option('theme'))->display('Pro/cart');
    }
    public function checkout(){
    	$this->theme(mc_option('theme'))->display('Pro/checkout');
    }
}