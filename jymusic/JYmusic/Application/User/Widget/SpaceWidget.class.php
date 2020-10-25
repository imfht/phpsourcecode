<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.jyuu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 战神巴蒂 <378020023.qq.com>
// +----------------------------------------------------------------------

namespace User\Widget;

use Think\Action;

/**
 * 分类widget
 */
class SpaceWidget extends Action{

    /* 显示用户自定义导航*/
    public function channel($uid){
    	//dump($this->user);
        $this->display('Widget/Space:channel');


    }
    
    
    /* 显示用户自定义导航*/
    public function indexunit(){
    	$user = $this->user;
    	$indexunit = explode(",",$user['indexunit']);
    	if (in_array('newShare',$indexunit)){
	        $newShare = M('songs')->where(array('up_uid'=>$user['uid']))->order('add_time desc')->field('id,name,artist_id,artist_name,album_id,album_name,genre_name,genre_id,listens,rater,add_time')->limit(6)->select();
	       	$this->assign('newShare',$newShare);
       	}
       	if (in_array('hotShare',$indexunit)){
	        $hotShare = M('songs')->where(array('up_uid'=>$user['uid']))->order('listens desc')->field('id,name,artist_id,artist_name,album_id,album_name,genre_name,genre_id,listens,rater,add_time')->limit(6)->select();
	       	$this->assign('hotShare',$hotShare);
       	}
       	
        $this->display('Widget/Space:indexunit');

    }
    
    /* 显示用户自定义bg*/
    public function bg(){
   		$path = '.'.__ROOT__.trim(C('VIEW_PATH'),'.').'User';	
   		$bnxml = @simplexml_load_file($path.'/space_skins/default_banner.xml');
    	if(is_object($bnxml)){
			$bnxml = json_encode($bnxml);
			$bnxml = json_decode($bnxml, true);
			$this->assign('default_banner',$bnxml['theme']);
		}
    	$bgxml = @simplexml_load_file($path.'/space_skins/default_bg.xml');
    	if(is_object($bgxml)){
			$bgxml = json_encode($bgxml);
			$bgxml = json_decode($bgxml, true);
			$this->assign('default_bg',$bgxml['theme']);
		}
		
        $this->display('Widget/Space:bg');

    }

}
