<?php

namespace Home\Controller;
/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class IndexController extends HomeController {

	//系统首页
    public function index(){
		/*header( 'Content-Type:text/html;charset=utf-8 ');  
        $link=mysql_connect("5618bd4f0a7b0.sh.cdb.myqcloud.com:14991","cdb_outerroot","aaa111456");
		if ($link){
			dump(213121);
		}else{
		
			dump('MYSQL连接失败，请确认配置是否正确！'); 
		}*/
		$this->display();       
    }
       
}