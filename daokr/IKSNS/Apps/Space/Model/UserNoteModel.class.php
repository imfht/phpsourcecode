<?php
/*
 * IKPHP爱客网 安装程序 @copyright (c) 2012-3000 IKPHP All Rights Reserved @author 小麦
* @Email:810578553@qq.com
* @小麦 修改时间2014年3月27日 23:44 地区基础类
*/
namespace Space\Model;
use Think\Model;

class UserNoteModel extends Model
{
	// 自动填充设置
	protected $_auto	 =	 array(
			array('addtime','time',self::MODEL_INSERT,'function'),
	);

	//获取一条日记
	public function getOneNote($map){
		$result = $this->where($map)->find();
		return $result;
	}
	//获取指定条数的日记
	public function getNotes($map,$limit = 10,$field='',$order='addtime desc'){
		$result = $this->field($field)->where($map)->order($order)->limit($limit)->select();
		return $result;
	}
	//获取推荐日记
	public function getRecommendNote($limit){
		$res = $this->where(array('isrecommend'=>1))->order('addtime desc')->limit($limit)->select();
		if($res){
			foreach($res as $key=>$item){
				$result[] = $item;
				$result[$key]['user'] = D('Common/User')->getOneUser($item['userid']);
				$result[$key]['content'] = ikhtml_text('note', $item['noteid'], $item['content']);
			}
			return $result;
		}else{
			return false;
		}
	}
	public function deleteOneNote($noteid){
		$where['noteid'] = $noteid;
		$strNote = $this->where($where)->find();
		if(!empty($strNote)){
			// 删除信息表
			$this->where($where)->delete();
			// 删除照片
			D('Common/Images')->delAllImage('note',$noteid);
			// 删除评论
			D('UserNoteComment')->where($where)->delete();
			return true;
		}else{
			return false;
		}
	}
	//最多浏览最多的
	public function getHotNotes($userid='',$limit=10){
		$where['isaudit'] = 1;
		$where['privacy'] = 1;
		!empty($userid) && $where['userid'] = $userid;
		$res = $this->where($where)->order('count_view desc')->limit($limit)->select();
		if($res){
			foreach($res as $key=>$item){
				$result[] = $item;
			}
			return $result;
		}else{
			return false;
		}
	}
}