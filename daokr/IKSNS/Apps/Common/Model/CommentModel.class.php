<?php
/*
 * IKPHP爱客网 安装程序 @copyright (c) 2012-3000 IKPHP All Rights Reserved @author 小麦
* @Email:810578553@qq.com
* @小麦 修改时间2014年4月9日 全站评论模型
*/
namespace Common\Model;
use Think\Model;

class CommentModel extends Model {

	//Refer二级循环，三级循环暂时免谈 获取评论
	public function recomment($referid){
		$where = array (
				'cid' => $referid,
		);
		$strComment = $this->where( $where )->find();
		$strComment['user'] = D('Common/User')->getOneUser($strComment['userid']);
		$strComment['content'] = hview($strComment['content']);
	
		return $strComment;
	}
	// 删除的单个或多个评论$commentid='1,2,3' 以逗号分隔
	public function delComment($commentid){
		$where['cid'] = array('exp',' IN ('.$commentid.') ');
		$arrComment = $this->field('typeid')->where($where)->select();
		if($arrComment){
			$this->where($where)->delete();
			return true;
		}else{
			return false;
		}
	}
	//获取最新回应type typeid 要知道有哪些typeid type
	public function getNewComment($where,$limit = 10){
		$arrresult = $this->where($where)->order('addtime desc')->limit($limit)->select();
		foreach ($arrresult as $key=>$item){
			$result[] = $item;
			$result[$key]['user'] = D('Common/User')->getOneUser($item['userid']);
		}
		return $result;
	}		
	
}