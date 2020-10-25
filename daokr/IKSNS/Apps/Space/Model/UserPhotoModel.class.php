<?php
/*
 * IKPHP爱客网 安装程序 @copyright (c) 2012-3000 IKPHP All Rights Reserved @author 小麦
* @Email:810578553@qq.com
* @小麦 修改时间2014年3月15日 23:44 地区基础类
*/
namespace Space\Model;
use Think\Model;

class UserPhotoModel extends Model
{
	// 自动填充设置
	protected $_auto	 =	 array(
			array('addtime','time',self::MODEL_INSERT,'function'),
	);

	//添加照片
    public function addPhoto($file,$userid,$albumid) {
    	$dir = 'photo/'.$userid.'/'.$albumid.'/';	
		$result = \Common\Util\Upload::saveLocalFile(
				$dir, 
				array(
					'width' =>C('ik_simg.width').','.C('ik_mimg.width').','.C('ik_bimg.width'),
    				'height'=>C('ik_simg.height').','.C('ik_mimg.height').','.C('ik_bimg.height')
				),'',4);
				
    	if (!$result ['error']) {
    		return $result;
		}else{
			return false;
		}
    }
    public function _after_insert($data, $options){
    	$album_mod = D('UserPhotoAlbum');
    	$album_mod->where(array('albumid'=>$data['albumid']))->setInc('count_photo');
    }
    //获取照片
    public function getPhotos($map,$order='addtime desc',$limit=''){
    	$res = $this->field('photoid')->where($map)->order($order)->limit($limit)->select();
    	foreach($res as $item){
    		$result[]= $this->getOnePhoto($item['photoid']);
    	}
    	return $result;
    }
    //获取单张图片
    public function getOnePhoto($pid){
    	$result = $this->where(array('photoid'=>$pid))->find();
    	if($result){
			$ext =  explode ( '.', $result['photoname']); 
			//图片大小
			$result['simg'] =  attach($result['photopath'].$ext[0].'_'.C('ik_simg.width').'_'.C('ik_simg.height').'.'.$ext[1]);
			$result['mimg'] =  attach($result['photopath'].$ext[0].'_'.C('ik_mimg.width').'_'.C('ik_mimg.height').'.'.$ext[1]);
			$result['bimg'] =  attach($result['photopath'].$ext[0].'_'.C('ik_bimg.width').'_'.C('ik_bimg.height').'.'.$ext[1]);
			$result['img']  =  attach($result['photopath'].$ext[0].'.'.$ext[1]);
    		return $result;
    	}else{
    		return false;
    	}
    }
    //根据pid 获取 album
    public function getAlbumInfo($pid){
    	$albumid = $this->where(array('photoid'=>$pid))->getField('albumid');
    	if($albumid){
    		return D('UserPhotoAlbum')->getOneAlbum($albumid);
    	}else{
    		return false;
    	}
    }
    //获取推荐照片
    public function getRecommendPhoto($limit){
    	$res = $this->field('photoid')->where(array('isrecommend'=>1))->order('addtime desc')->limit($limit)->select();
	    if($res){
	    	$result = array();
	        foreach ($res as $item){
    			$result[] = $this->getOnePhoto($item['photoid']); 
    		}
    		return  $result;
	    }else{
	    	return false;
	    }
    }
    //删除照片 单个或多个 $photoid = '1,2,3' 以逗号分隔
    public function delPhoto($photoid){
    	$where['photoid'] = array('exp',' IN ('.$photoid.') ');
    	$arrPhoto = $this->field('albumid')->where($where)->select();
    	if($arrPhoto){
    		$this->where($where)->delete();
    		//删除评论
    		D('UserPhotoComment')->where($where)->delete();
    		foreach ($arrPhoto as $item){
    			D('UserPhotoAlbum')->where(array('albumid'=>$item['albumid']))->setDec('count_photo');
    		}
    		return true;    		
    	}else{
    		return false;
    	}

    }



}