<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;

class PublicController extends HomeBaseController {

    //输出验证码
    public function verify(){
    	$w=I('get.w',0,'intval');
    	$h=I('get.h',0,'intval');
        $config=array(
            'length'=>4,
            'fontSize'=>12,
            'useCurve'=>false,
            'useNoise'=>false,
	        'imageH'    =>  $h,
	        'imageW'    =>  $w,
	        'bg'        =>  array(255, 255, 255),  // 背景颜色
            'fontttf' => '6.ttf',
            'codeSet' => '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ',
        );
        $verify = new \Think\Verify($config);
        $verify->entry(1);
    }

    private function checkVerify($code){
        $Verify = new \Think\Verify();
        if(!$Verify->check($code, 1)) return false;
        return true;
    }
    	
}
