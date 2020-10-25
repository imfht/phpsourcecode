<?php
/*
 * IKPHP爱客网 安装程序 @copyright (c) 2012-3000 IKPHP All Rights Reserved @author 小麦
* @Email:810578553@qq.com
* @小麦 修改时间2014年3月15日 23:44 地区基础类
*/
namespace Space\Model;
use Think\Model;

class UserPhotoCommentModel extends Model
{
	// 自动验证设置
	protected $_validate	 =	 array(
			array('content','require','评论内容必须填写'),
	);
	
	//Refer二级循环，三级循环暂时免谈 获取评论
	public function recomment($referid){
		$where = array (
				'commentid' => $referid,
		);
		$strComment = $this->where( $where )->find();
		$strComment['user'] = D('user')->getOneUser($strComment['userid']);
		$strComment['content'] = h($strComment['content']);
	
		return $strComment;
	}
	// 删除的单个或多个评论$commentid='1,2,3' 以逗号分隔
	public function delComment($commentid){
		$where['commentid'] = array('exp',' IN ('.$commentid.') ');
		$arrComment = $this->field('photoid')->where($where)->select();

		if($arrComment){
			$this->where($where)->delete();
			foreach ($arrComment as $item){
				D('UserPhoto')->where(array('photoid'=>$item['photoid']))->setDec('count_comment');
			}
			return true;
		}else{
			return false;
		}
	}
	// 获取指定 appid 评论
	public function getCommentByid($cid,$order='addtime asc',$limit='1'){
		$arrComment = $this->where(array('commentid'=>$cid))->order($order)->limit($limit)->select();
		if($arrComment){
			foreach ($arrComment as $key=>$item){
				$result[] = $item;
				$result[$key]['user'] = D('user')->getOneUser($item['userid']);
			}
			return $result;
		}else{
			return false;
		}		
	}
	public function _after_insert($data, $options){
		$photo_mod = D('UserPhoto');
		$photo_mod->where(array('photoid'=>$data['photoid']))->setInc('count_comment');
	}
	//获取最新回应
	public function getNewComment($userid,$limit = 10){
		$where['userid'] = $userid;
		$arrresult = $this->where($where)->order('addtime desc')->limit($limit)->select();
		foreach ($arrresult as $key=>$item){
			$result[] = $item;
			$result[$key]['user'] = D('user')->getOneUser($item['userid']);
		}
		return $result;
	}
}