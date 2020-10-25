<?php
/*
 * IKPHP爱客网 安装程序 @copyright (c) 2012-3000 IKPHP All Rights Reserved @author 小麦
* @Email:810578553@qq.com
* @小麦 修改时间2014年3月15日 23:44 地区基础类
*/
namespace Space\Model;
use Think\Model;

class UserPhotoAlbumModel extends Model
{
	// 自动验证设置
	protected $_validate	 =	 array(
			array('albumname','require','相册名称必须填写',1)
			
	);
	// 自动填充设置
	protected $_auto	 =	 array(
			array('uptime','time',self::MODEL_UPDATE,'function'),
			array('addtime','time',self::MODEL_INSERT,'function'),
			array('uptime','time',self::MODEL_INSERT,'function'),
	);
	
	//获取相册列表
	public function getAlbums($map,$order='addtime desc',$limit = '')
	{
		$res = $this->field('albumid')->where($map)->order($order)->limit($limit)->select();
		if($res){
			foreach($res as $key=>$item){
				$result[$key] = $this->getOneAlbum($item['albumid']);
			}
			return $result;
		}else{
			return false;
		}
	}
	//获取一个相册
	public function getOneAlbum($id){
		$result = $this->where(array('albumid'=>$id))->find();
		if($result){
			$strAlbum = $result;
			if(!empty($result['path'])){
				//存在封面图片路径
				$ext =  explode ( '.', $result['albumface']);
				//图片大小
				$strAlbum['simg'] =  attach($result['path'].$ext[0].'_'.C('ik_simg.width').'_'.C('ik_simg.height').'.'.$ext[1]);
				$strAlbum['mimg'] =  attach($result['path'].$ext[0].'_'.C('ik_mimg.width').'_'.C('ik_mimg.height').'.'.$ext[1]);
			}else{
				$strAlbum['simg'] = $strAlbum['mimg'] = __ROOT__ . "/Public/Static/images/photo_album.png";
			}
			return $strAlbum;
		}else{
			return false;
		}		
	}
	//获取推荐相册
	public function getRecommendAlbum($limit){
	    $res = $this->field('albumid')->where(array('isrecommend'=>1))->order('addtime desc')->limit($limit)->select();
		if($res){
			foreach($res as $key=>$item){
				$result[$key] = $this->getOneAlbum($item['albumid']);
			}
			return $result;
		}else{
			return false;
		}
	}	
}