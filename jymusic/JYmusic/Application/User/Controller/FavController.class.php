<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace User\Controller;
class FavController extends UserController {
    /**
	* 收藏歌曲
	*/    
    public function index(){
    	
    	$ids = M('UserFav')->where(array('uid'=>UID,'type'=>'song'))->field('music_id')->select();
    	if(!empty($ids)){
	    	$ids = array_column($ids, 'music_id');
	    	$map['id'] = array('in',$ids);
	    	$list = $this->lists('Songs',$map,'id desc','id,name,introduce,cover_url,up_uid,up_uname,artist_id,artist_name,album_id,album_name,genre_name,genre_id,listens,likes,favtimes,rater,add_time');//获取歌曲数据集
	    	$this->assign('list', $list);
    	}
    	$this->meat_title = '我的歌曲收藏 - '.C('WEB_SITE_TITLE');
		$this->display();
    }
       
    /**
	* 收藏专辑
	*/    
    public function album(){
    	$ids = M('UserFav')->where(array('uid'=>UID,'type'=>'album'))->field('music_id')->select();
    	if(!empty($ids)){
	    	$ids = array_column($ids, 'music_id');
	    	$map['id'] = array('in',$ids);
	    	$list = $this->lists('Album',$map,'id desc','id,name,introduce,cover_url,add_uid,add_uname,artist_id,artist_name,type_id,type_name,genre_name,genre_id,hits,rater,likes,favtimes,add_time');//获取歌曲数据集
	    	$this->assign('list', $list);
    	}
    	$this->meat_title = '我的专辑收藏 - '.C('WEB_SITE_TITLE');
		$this->display();
    }
    
    
    /**
	* 收藏歌手
	*/    
    public function artist(){
    	$ids = M('UserFav')->where(array('uid'=>UID,'type'=>'artist'))->field('music_id')->select();
    	if(!empty($ids)){
	    	$ids = array_column($ids, 'music_id');
	    	$map['id'] = array('in',$ids);
	    	$list = $this->lists('Artist',$map,'id desc','id,name,introduce,cover_url,type_id,type_name,region,region_id,hits,rater,likes,favtimes,add_time');//获取歌曲数据集
	    	$this->assign('list', $list);
	    }
    	$this->meat_title = '我的艺术家收藏 - '.C('WEB_SITE_TITLE');
		$this->display();
    }
}