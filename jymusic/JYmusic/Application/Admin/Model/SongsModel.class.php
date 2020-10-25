<?php
// +----------------------------------------------------------------------
// | Author: 战神巴蒂<378020023@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Model;
use Think\Model;


class SongsModel extends Model {

    protected $_validate = array(
        array('name', 'require', '名称不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),
        array('music_url', 'require', 'URL不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('artist_name', 'require', '所属艺术家不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        //array('genre_name', 'require', '所属曲风不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('add_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('up_uid','getUid', self::MODEL_BOTH,'callback'),
        array('up_uname','getName',3,'callback'), // 对name字段在新增和编辑的时候回调getName方法
        array('music_down','getDownPath',3,'callback'),
        array('down_file_id','getDownFileid',3,'callback'),
        array('status', '1', self::MODEL_BOTH),
        array('genre_name', 'getGnreName', self::MODEL_BOTH,'callback'),
        array('tags', 'geTags', self::MODEL_BOTH,'callback'),
        array('listens', 'getListens', self::MODEL_BOTH,'callback'),
		array('cover_url', 'getcover', self::MODEL_BOTH,'callback'),
    );
    
    /**
    * 获取下载地址,防止没有填写
    */          
    function getDownPath() {    	
    	if(!empty($_POST['music_down'])){
    		return $_POST['music_down'];   		
    	}else{   		
    		return  $_POST['music_url'];
    	}
    	
    }
    
   /**
    * 获取上传用户id
    */  
    function getUid() { 
    	if ($id = $_POST['up_uid']) {
    		return $id;
    	}else{
    		return UID;
    	}
    }
   
   /**
    * 获取上传用户昵称
    */  
    function getName() { 
    	if ($id = $_POST['up_uid']) {
    		return get_nickname($id);
    	}else{
    		return get_nickname(UID);
    	}
    }        
    
   	/**
    * 获取曲风名称
    */  
    function getGnreName() {    	
    	return get_genre_name($_POST['genre_id']);
    }
    
    /*
    * 获取标签
    */
    
    function geTags () {
    	$tag  = trim($_POST['tags']);   	
    	if(!empty($tag)){
    		$t  = M('tag'); 
    		$tag = explode(',',$tag);    		
    		foreach ($tag as $k => $v) {
    			$id = $t->getFieldByName($v,'id');    		 	
    		 	$t->where(array('id'=>$id))->setInc('count');
    		 	$tags .= ','.$id;
    		}    		
    	}
    	return trim($tags,',');
    }
    /**
    * 获取试听数
    */ 
    function getListens() { 
    	return setrand($_POST['listens']);
    }
   /**
    * 获取下载地址的文件ID
    */   
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
			if($id = $_POST['album_id']){
				return  M('Album')->getFieldById($id,'cover_id');
			}else{
				return __ROOT__.'/Uploads/Picture/song_cover.jpg';
			}
		}
    }
    
    
    /**
     * 删除状态为-1的数据
     * @return true 删除成功， false 删除失败
     * @author huajie <banhuajie@163.com>
     */
    public function remove(){
        //查询假删除的基础数据
        $map = array('status'=> -1);
        $base_list = $this->where($map)->field('id,listen_file_id,down_file_id')->select();
        //删除本地音乐文件
        $file = M('file');
        $ids = null;
        
        foreach ($base_list as $key=>$v){
        	$lid = $v['listen_file_id'];
        	$did = $v['down_file_id'];
            if ($lid && $lid == $did){ //试听文件并且 试听文件和下载文件相同
            	$map['id'] = $lid;
            	$save = $file->where($map)->field('savepath,savename')->find();
            	unlink($save['savepath'].$save['savename']);
            	$file->where($map)->delete();            
            }elseif ($lid && $did){
            	$map['id'] = $tid;
            	$map2['id'] = $did;
            	$save = $file->where($map)->field('savepath,savename')->find();
            	$save2 = $file->where($map2)->field('savepath,savename')->find();
            	unlink($save['savepath'].$save['savename']);
            	unlink($save2['savepath'].$save2['savename']);
            	$file->where($map)->delete();
            	$file->where($map2)->delete();
            }elseif($did){
            	$map['id'] = $did;
            	$save = $file->where($map)->field('savepath,savename')->find();
            	unlink($save['savepath'].$save['savename']);
            	$file->where($map)->delete();
            }
            $ids.=$v['id'].',';
        }

        //删除基础数据
        if(!empty($ids)){
            $res = $this->where( array( 'id'=>array( 'IN',trim($ids,',') ) ) )->delete();
        }

        return $res;
    }
}
