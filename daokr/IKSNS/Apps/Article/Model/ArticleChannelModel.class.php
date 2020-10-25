<?php
/*
 * IKPHP爱客网 安装程序 @copyright (c) 2012-3000 IKPHP All Rights Reserved @author 小麦
* @Email:810578553@qq.com
* @小麦 修改时间2014年4月6日 14:33
*/
namespace Article\Model;
use Think\Model;

class ArticleChannelModel extends Model {
	
	// 获取全部频道
	public function getAllChannel($where =''){
		$result = $this->where($where)->select ();
		return $result;
	}
	// 获取指定nameid的频道
	public function getOneChannel($nameid){
		$where = array('nameid'=>$nameid);
		$result = $this->where($where)->find ();
		return $result;
	}	
	
	
}