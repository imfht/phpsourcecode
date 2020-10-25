<?php
namespace app\common\model;

use think\Model;

class Navigation extends Model {

	public $fenLei = [
		1 => '电脑顶部',
		2 => '电脑主导航',
		3 => '电脑底部',
		4 => '电脑会员中心',
		5 => '手机首页',
		6 => '手机底部',
		7 => '手机侧栏',
		8 => '手机会员中心',
	];

	public function daoHang($type=[]){
		$list = Navigation::where('type','in',$type)->where(['closed'=> 1])->order('pid ASC, type ASC,orderby ASC')->select();
		$treeClass = new \expand\Tree();
		$list = $treeClass->create($list);
		foreach($list as $k => $v){
			$fuJi = Navigation::where( ['pid' => $v['id'],'closed'=> 1 ] )->order('pid ASC, type ASC,orderby ASC')->count();
			if( $fuJi > 0){
				$list[$k]['url'] = null;
			}
		}
		return $list;
	}





}
