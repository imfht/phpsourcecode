<?php
namespace App\Util;

use Illuminate\Support\Facades\Auth;
use App\User;
use App\TreeTrunk;

class TreeUtil{
	
	// 树 叶子和树干的表示
	const TRUNK = 1;
	const LEAF = 2;
	// 树干类型的区分，1 表示树干节点代表 组织部门， 2 表示树干节点代表 物资信息的分类
	const TYPE_ORGANIZATION_TRUNK = 1;
	const TYPE_MATERIAL_TRUNK = 2;
	/*
	 * 前端 jstree 节点的 id 转换为 数据库记录的 id 
	 * @param $treeid 前端jstree节点的 id
	 * return $recordid 数据库记录的 id
	 */
	public static function nodeidToRecordid($nodeid){
		
		if($nodeid == '#' || $nodeid == '0'){
			return 0;
		}
		return substr($nodeid, 0, -1);	// 去掉最后一个字符	
	}
	/*
	 * 数据库记录的 id 转换为 jstree 的 id.
	 */
	public static function recordidToNodeid($recordid,$leafOrTrunk){
		if($recordid == '0'){
			return '#';
		}
		return ($recordid).( $leafOrTrunk );
	}
	/**
	 * 判断节点的类型，树枝节点或者叶子节点
	 * 返回 1 (TreeUtil::TRUNK) 或  2(TreeUtil::LEAF)
	 */
	public static function getNodeType( $nodeid ){
		return substr($nodeid, -1, 1);
	}
	/*
	 * 从子节点开始向根节点，获取当前用户的最近一级管理员的 id
	 * 如果返回空的话，表名系统错误
	 */
	public static function getFirstManage($trunkId,$delete){
		// 1. 叶子节点本身在树的根目录上. 挂载在公司根目录的所有叶子节点的 tree_trunk_id 都为 0
		if($trunkId == 0){
			return User::where('company_id',session(SessionUtil::COMPANY)->id)
			->where('tree_trunk_id',0)
			->where('job_type',User::USER_JOB_MANAGER)
			->where('delete','<',$delete)
			->first();
		}
		// 1. 叶子节点本身不在树的根目录上.
		$tempUser = User::where('tree_trunk_id',$trunkId)
			->where('job_type',User::USER_JOB_MANAGER)
			->where('delete','<',$delete)
			->first();
		if(empty($tempUser) == false){
			return $tempUser;
		}
		$trunk = TreeTrunk::findOrFail($trunkId);
		return TreeUtil::getFirstManage($trunk->parent_id, $delete);
	}
	/**
	 * 获取所在公司的部门路径
	 * @param unknown $companyId
	 * @param unknown $trunkId
	 * @return unknown|string
	 */
	public static function getParentTrunkNames($companyId,$trunkId){
		// 1. 叶子节点本身在树的根目录上
		if($trunkId == 0){
			return session(SessionUtil::COMPANY)->name;
		}
		// 2. 在数的二级目录上。也就是挂载的分支在树的根目录上
		$trunk = TreeTrunk::findOrFail($trunkId);
		if($trunk->parent_id == 0){
			return $trunk->name;
		}
		// 3. 其本身不在根目录，且其挂载的分支也不在树的根目录上
		return TreeUtil::getParentTrunkNames($companyId,$trunk->parent_id).'##'.$trunk->name;
	}
	
}