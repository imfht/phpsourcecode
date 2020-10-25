<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class IndexController extends AdminController {
	public function index(){		
        $count=array();	
        $S = M('Songs');
    	$count['songs']  =  $S->count();//获取歌曲总数
    	$count['album']  =  M('Album')->count();//获取专辑总数
    	$count['genre']  =  M('Genre')->count();//获取曲风总数
    	$count['user']  =  M('Member')->count();//获取用户总数
		$count['artist']  =  M('Artist')->count();//获取艺术家总数
    	$version = JYMUSIC_VERSION;
    	$newSong = $S->where(array('status'=>1))->field('name,add_time')->order('id desc')->limit(6)->select();
    	$msglist = M('message')->where(array('to_uid'=>0))->field('post_time,title,content')->order('id desc')->limit(6)->select();    	    	    	
    	$this->assign('newSong',$newSong);
		$this->assign('count',$count);		
		$this->assign('msglist',$msglist);
        $this->meta_title = '管理首页';
        $this->display();
    }
    
    public function checkUpdate() {
    	header("Content-Type:text/html;charset=UTF-8");	 
		if(extension_loaded('curl')){
	        $url = 'http://115.159.39.64/index.php?m=home&c=CheckVersion';
	        $params = array(
	            'version' => JYMUSIC_VERSION,
	            'updateTime' => JY_UPDATE_TIME,
	            'domain'  => $_SERVER['HTTP_HOST'],
	            'auth'    => sha1(C('DATA_AUTH_KEY')),
	        );	
	        $vars = http_build_query($params);
	        $opts = array(
	            CURLOPT_TIMEOUT        => 5,
	            CURLOPT_RETURNTRANSFER => 1,
	            CURLOPT_URL            => $url,
	            CURLOPT_POST           => 1,
	            CURLOPT_POSTFIELDS     => $vars,
	            CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'],
	        );	
	        /* 初始化并执行curl请求 */
	        $ch = curl_init();
	        curl_setopt_array($ch, $opts);
	        $data  = curl_exec($ch);
	        $error = curl_error($ch);
	        curl_close($ch);        	        
	        if(!empty($data) && strlen($data)<400 ){
                $update = $array=explode(',',$data);                
                //检测是否是新版本
                $return['status'] = 1;
                $return['info'] = $update[2];                
            }
           $this->ajaxReturn($return);
	   	}else{	   	
			$this->error('程序无法自动升级,PHP没有开启"curl"扩展。请下载安装包，手动升级！');   			
	   	}
    
    }    
    
    public function clearCache() {
    	$dirname = './Runtime/';
		//清文件缓存
		$dirs	=	array($dirname);		
		//清理缓存
		foreach($dirs as $value) {
			rmdirr($value);			
		}
		$this->success("已清理!");
		@mkdir($dirname,0777,true);
    
    } 	
}
