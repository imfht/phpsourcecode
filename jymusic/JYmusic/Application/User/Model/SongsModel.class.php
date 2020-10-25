<?php
// +----------------------------------------------------------------------
// | Author: 战神~~巴蒂
// +----------------------------------------------------------------------

namespace User\Model;
use Think\Model;


class SongsModel extends Model {
	protected $_map = array(         
		'song' 	=>'name',       
		'url'  	=>'music_url',
		'artist'=>'artist_name',
		'genre'=>'genre_id',   
	);
	
    protected $_validate = array(
        array('name', 'require', '名称不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),
        array('music_url', 'require', 'URL不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('genre_id', 'require', '所属曲风不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('score', 'number', '必须是数字', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),
        //array('genre_name', 'require', '所属曲风不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('add_time', NOW_TIME, self::MODEL_INSERT),
        array('up_uid', UID, self::MODEL_INSERT),
        array('up_uname','getName',3,'callback'),
        array('genre_name','getGenreName',3,'callback'),
        array('down_file_id','getDownFileid',3,'callback'),
		array('album_name', 'getAlbumName',3,'callback'), 
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('add_time', NOW_TIME, self::MODEL_BOTH),
        array('cover_url', 'getcover', self::MODEL_BOTH,'callback'),
        array('status', 2, self::MODEL_BOTH),
    );
         
    protected  function getName () {
     
     	return get_nickName(UID);
    }
	
	protected  function getGenreName () {
     	$genre =  I('post.genre');
     	if (!empty($genre)){
     		return M('Genre')->getFieldById($genre,'name');
     	}else{
     		return null ;
     	}
    }
    
   	protected  function getAlbumName () {
     	$album =  I('post.album');
     	if (!empty($album)){
     		return M('Genre')->getFieldById($album,'name');
     	}else{     		
     		return '单曲';
     	}
    }
    
    protected  function getArtistName () {
     	$artist =  I('post.artist');
     	if (!empty($artist)){
     		return M('Genre')->getFieldById($artist,'name');
     	}else{     		
     		return '佚名';
     	}
    }
    
    protected  function getDownFileid () {
     	$id =  I('post.down_file_id');
     	if (!empty($id)){
     		return $id;
     	}else{
     		$listenid =  I('post.listen_file_id');
     		if (!empty($listenid)) {   		
     			return $listenid;
     		}else{
     			return 0;
     		}
     	}
    }
    
   	function getcover() {
		$url = $_POST['cover_url'];
   		if(!empty($url)) {
			return $url;
		}else{
			return __ROOT__.'/Uploads/Picture/songs_cover.jpg';
		}
    }
}
