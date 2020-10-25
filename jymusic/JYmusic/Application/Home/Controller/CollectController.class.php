<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Home\Controller;
use Think\Controller;

class CollectController extends HomeController {
    public function index(){
    	import('JYmusic.Snoopy');
    	$snoopy = new /Snoopy;
    	//$this->display();	   	    		   	
    }
    
   	public function hw($id){
   		//set_time_limit(20);//设置超时时间
   		if ($id){
    		$db = new Dbank;
    		$db	->link($id);
    	}else{
    		$this-error('参数错误');
    	}  		
    }
}