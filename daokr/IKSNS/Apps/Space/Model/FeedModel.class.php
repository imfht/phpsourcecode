<?php
/*
 * IKPHP爱客网 安装程序 @copyright (c) 2012-3000 IKPHP All Rights Reserved @author 小麦
* @Email:810578553@qq.com
* @小麦 修改时间2014年3月28日 23:44 
*/
namespace Space\Model;
use Think\Model;

class FeedModel extends Model
{
	// 自动填充设置
	protected $_auto	 =	 array(
			array('addtime','time',self::MODEL_INSERT,'function'),
	);

	public function addFeed($data){
		if(!false == $this->create($data)){
			$id = $this->add();
			return $id;
		}else{
			return false;
		}
	}
	public function addFeedData($feedid,$tpl,$tpldata){
		//添加模版数据
		$fdata = array(
				'feedid' => $feedid,
				'template' => $tpl,
				'feeddata' => serialize($tpldata),
		);
		$res = M('feed_data')->add($fdata);
		if($res){
			return true;
		}else{
			return false;
		}
	}
	public function getOneFeed($feedid,$userid){
		$result = $this->where(array('feedid'=>$feedid,'userid'=>$userid))->find();
		return $result;
	}
	//删除deleteFeed
	public function deleteFeed($feeid){
		$where['feedid'] = array('exp',' IN ('.$feeid.') ');
		$arr = $this->field('feedid,topicid')->where($where)->select();
		if($arr){
			//删除关联数据
			M('feed_data')->where($where)->delete();//话题内容
			M('feed_images')->where($where)->delete();//附件图片
			foreach ($arr as $item){
				$map['topicid'] = array('exp',' IN ('.$item['topicid'].') ');
				D('feed_topic')->where($map)->setDec('count_topic');
			}
			$this->where($where)->delete();
			return true;
		}else{
			return false;
		}
	}
	
}