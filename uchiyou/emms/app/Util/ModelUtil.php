<?php

namespace App\Util;

use App\TreeTrunk;
use App\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ModelUtil {
	
	// 与操作相关的权限字段，不存在数据库中
	const WHERE_PERSON = 'person';
	const WHERE_MANAGE = 'manage';
	/*
	 * 与删除相关的权限字段，不存在数据库中
	 * 该模型的 delete 字段
	 * 由于权限管理的关系，导致字段的大小顺序很重要，不要随便改。
	 */
	const DELETE_NORMAL = 1;
	const DELETE_SELF = 2;
	const DELETE_MANAGER_DEPARTMENT = 3;
	const DELETE_MANAGER_SUPER = 4;
	const CONST_MAX_NUMBER = 100;
	/**
	 * 获取 $trunkId 分支下的所有有效分支。可以帮助管理员过滤掉已经删除的部门
	 * @param $trunkId 需要获取该分支 id 下的所有分支
	 * @returns $trunkIdArray 当前管理员能管理的所有分支
	 * 最小化递归的获取部门的所有子分支
	 */
	public static function getUserSubtrunk($trunkId,$hasDeletedTrunk = FALSE) {
		$trunkIdArray = [ ];
		$trunkIdArray [] = $trunkId;
		$trunks = TreeTrunk::where ( 'parent_id', $trunkId )
		->where ( 'delete', '<', 
				$hasDeletedTrunk?ModelUtil::getDeleteLevel ():ModelUtil::CONST_MAX_NUMBER )
		->get ();
		if (empty ( $trunks ) === false) {
			foreach ( $trunks as $trunk ) {
				$trunkIdArray = array_merge ( $trunkIdArray, ModelUtil::getUserSubtrunk ( $trunk->id ) );
			}
		}
		return $trunkIdArray;
	}
	/*
	 * 根据不同权限删除数据
	 */
	public static function delete($modelName, $id) {
		$modelName = '\\App\\' . $modelName;
		$table = $modelName::findOrFail ( $id );
		$user = Auth::user ();
		if ($user->job_type == User::USER_JOB_MANAGER) {
			if ($user->tree_trunk_id == 0) {
				$table->delete = ModelUtil::DELETE_MANAGER_SUPER;
			} else {
				$table->delete = ModelUtil::DELETE_MANAGER_DEPARTMENT;
			}
		} else {
			$table->delete = ModelUtil::DELETE_SELF;
		}
		return $table->save () ? true : false;
	}
	/*
	 * 获取当前用户删除的级别
	 */
	public static function getDeleteLevel() {
		$user = Auth::user ();
		if ($user->job_type == User::USER_JOB_MANAGER) {
			if ($user->tree_trunk_id == 0) {
				return ModelUtil::DELETE_MANAGER_SUPER;
			} else {
				return ModelUtil::DELETE_MANAGER_DEPARTMENT;
			}
		} else {
			return ModelUtil::DELETE_SELF;
		}
	}
	/*
	 * 获取分页中分支数组的 id
	 */
	public static function getTrunkIdArray(LengthAwarePaginator $treeTrunks) {
		$result = [ ];
		foreach ( $treeTrunks as $treeTrunk ) {
			$result = $treeTrunk->id;
		}
		return $result;
	}
	/**
	 *
	 * @param Request $request
	 *        	从 request 获取参数
	 * @param unknown $modelName
	 *        	和 从 request 中获取的参数一起拼接对 materialName 的sql 查询条件
	 */
	public static function getHistorySearchParams(Request $request, $tableName) {

			$type = $request->get ( 'type' );
			$content = $request->get ( 'content' );
			$column = '';
			switch ($type) {
				// 不一定是 'mm_material ' 表
				case 'materialName' :
					$column = $tableName . '.name';
					break;
				// 用户名，一定是 'users' 表
				case 'userName' :
					$column = 'users.name';
					break;
				default :
					abort ( 404, '不存在这种类型的历史记录搜索' );
			}
			return array ($column,$content);
	}
	public static function isUserInCurrentUserSubTree($curUserTrunkId,$trunkId){
		$userIds = ModelUtil::getUserSubtrunk($curUserTrunkId);
		foreach ($userIds as $id){
			if($trunkId == $id){
				return true;
			}
		}
		return false;
	}
}