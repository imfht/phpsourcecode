<?php
/*
 * IKPHP爱客网 安装程序 @copyright (c) 2012-3000 IKPHP All Rights Reserved @author 小麦
* @Email:810578553@qq.com
* @小麦 修改时间2014年4月6日 14:33
*/
namespace Article\Model;
use Think\Model;

class ArticleCateModel extends Model {
	
	// 根据频道nameid获取全部分类
	public function getCateByNameid($nameid){
		$where = array('nameid'=>$nameid);
		$result = $this->where ( $where )->select ();
		return $result;
	}
	// 获取全部分类
	public function getAllCate($nameid=''){
		if(!empty($nameid)){
			$where = array('nameid'=>$nameid);
		}else{
			$where = '';
		}
		$result = $this->where($where)->order('orderid asc')->select ();
		return $result;		
	}
	// 根据cateid 获取分类
	public function getOneCate($cateid){
		$where = array('cateid'=>$cateid);
		$result = $this->where ( $where )->find ();
		return $result;		
	}

}