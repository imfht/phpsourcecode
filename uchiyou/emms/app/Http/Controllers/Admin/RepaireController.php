<?php

namespace App\Http\Controllers\Admin;

use App\Config;
use App\Http\Controllers\Controller;
use App\Material;
use App\Message;
use App\RepaireRecord;
use App\User;
use App\UsingRecord;
use App\Util\DateUtil;
use App\Util\MessageUtil;
use App\Util\ModelUtil;
use App\Util\SessionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use App\Util\TreeUtil;

class RepaireController extends Controller {
	private function getHistoryParametersArray($type) {
		$user = Auth::user ();
		$operator = '=';
		$status = RepaireRecord::REPAIRERECORD_STATUS_APPLY;
		switch ($type) {
			case 'applys' :
				$operator = '=';
				$status = RepaireRecord::REPAIRERECORD_STATUS_APPLY;
				break;
			case 'all' :
				$operator = '!=';
				$status = '';
				break;
			default :
				abort ( 404, '没有此类型的维修历史记录' );
		}
		// 权限分配.划分权限是为了避免使用 in ，提升查询效率
		$delete = ModelUtil::DELETE_MANAGER_DEPARTMENT;
		$column = 'mm_material.tree_trunk_id';
		$method = 'whereIn';
		$value = ModelUtil::getUserSubtrunk ( $user->tree_trunk_id,true );
		if ($user->tree_trunk_id == 0) {
			$delete = ModelUtil::DELETE_MANAGER_SUPER;
			$column = 'mm_repaire_record.company_id';
			$method = 'where';
			$value = $user->company_id;
		}
		return array (
				$user->company_id,
				$status,
				$operator,
				$delete,
				$column,
				$method,
				$value 
		);
	}
	/**
	 * 显示维修记录
	 */
	public function showRepaireHistory($type) {
		$params = $this->getHistoryParametersArray ( $type );
		// 不要轻易的将表名称简写，注意和 $column 对应
		$repaireRecords = RepaireRecord::getRecord ( $params [0], $params [1], $params [2], $params [3], $params [4], $params [5], $params [6] );
		return $this->getHistoryView ( $repaireRecords ,$type);
	}
	private function getHistoryView($records,$type) {
		return view ( 'MaterialLifecycle/showRepaireRecord', [ 
				'repaireRecords' => $records ,
				'type' => $type, 
		] );
	}
	public function showHistorySearch(Request $request,$type){
	
		$searchParams = ModelUtil::getHistorySearchParams($request, 'mm_material');
		$params = $this->getHistoryParametersArray($type);
		$records = RepaireRecord::getSearchRecordsResult(
				$searchParams[0], $searchParams[1],
				$params[0],$params[1],$params[2],$params[3],
				$params[4],$params[5],$params[6]);
		return $this->getHistoryView($records,$type);
	}
	/**
	 * 申请维修
	 */
	public function applyRepaire(Request $request, $materialId) {
		// 数据验证
		$this->validate ( $request, [ 
				'description' => 'required'  // 必填
		] );
		$user = Auth::user ();
		$material = Material::findOrFail ( $materialId );
		// 1. 存储一条维修记录
		$repaireRecord = new RepaireRecord ();
		$repaireRecord->fault_description = $request->get ( 'description' );
		$repaireRecord->user_id = $user->id;
		$repaireRecord->company_id = $user->company_id;
		$repaireRecord->material_id = $materialId;
		$repaireRecord->status = RepaireRecord::REPAIRERECORD_STATUS_APPLY;
		if ($repaireRecord->save ()) {
			// 2 修改物资状态
			$material->status = Material::MATERIAL_STATUS_PROBLEM;
			$material->save ();
			// 3 如果报告故障的人正在使用该物资，现实中应该自动归还
			$usingRecord = UsingRecord::where ( 'material_id', $materialId )
			->where ( 'deadline', '>', date ( DateUtil::FORMAT ) )
			->where ( 'user_id', '=', $user->id )
			->first ();

			if (empty ( $usingRecord ) == false) {
				$usingRecord->deadline = $usingRecord->startTime;
				$usingRecord->save ();
			}
			// 4 发现对应的维修人
			$repaireMan = TreeUtil::getFirstManage($user->tree_trunk_id, ModelUtil::getDeleteLevel());
			if (empty ( $repaireMan )) {
					abort ( 500 ,'没有找到该公司的管理者');
				}
			// 5 给维修人生成一条对应的通知消息
			$departmentName = session(SessionUtil::COMPANY)->name;
			$treeTrunk = session( SessionUtil::DEPARTMENT );
			if (empty ( $treeTrunk ) === false) {
				$departmentName = $treeTrunk->name;
			}
			Message::saveMessage ( $user->id, $repaireMan->id,
					MessageUtil::getRepaireMessage ( $material, 
							$repaireRecord->fault_description, 
							$departmentName, $user->name ), 
					MessageUtil::EVENT_READ_ONCE, '' );
		}
		return $user->job_type == User::USER_JOB_MANAGER?
		Redirect::to('/admin/material/repaire/history/applys'):Redirect::back ();
	}
	/**
	 * 对设备维修后的操作，可以是维修成功，失败或报废
	 */
	public function repaireResult(Request $request, $recordId, $result) {
		// 1. 根据不同的结果，设置不同的状态
		$repaireStatus = RepaireRecord::REPAIRERECORD_STATUS_SUCESS;
		$materialStatus = Material::MATERIAL_STATUS_AVAILABLE;
		$delete = ModelUtil::DELETE_NORMAL;
		if ($result == 'shutdown') {
			$repaireStatus = RepaireRecord::REPAIRERECORD_STATUS_SHUTDOWN;
			$materialStatus = Material::MATERIAL_STATUS_DISCARD;
			$delete = ModelUtil::getDeleteLevel();
		} elseif ($result == 'sucess') {
			$repaireStatus = RepaireRecord::REPAIRERECORD_STATUS_SUCESS;
			$materialStatus = Material::MATERIAL_STATUS_AVAILABLE;
		} else {
			abort ( 404 ,'没有这种类型的修复操作');
		}
		// 2 保存修改后的状态
		$repaireRecord = RepaireRecord::findOrFail ( $recordId );
		$repaireRecord->status = $repaireStatus;
		$repaireRecord->save ();
		$material = Material::findOrFail ( $repaireRecord->material_id );
		$material->status = $materialStatus;
		$material->delete = $delete;
		$material->save ();
		return $this->jsonResult(0,[],'sucess');
	}
	public function getWaitRepaireCount(){
		$user = Auth::user();
		return DB::table('mm_repaire_record')
		->leftJoin('mm_material','mm_material.id','=','mm_repaire_record.material_id')
		->where('mm_repaire_record.company_id','=',$user->company_id)
		->where('mm_repaire_record.status','=',RepaireRecord::REPAIRERECORD_STATUS_APPLY)
		->whereIn('mm_material.tree_trunk_id',ModelUtil::getUserSubtrunk($user->tree_trunk_id))
		->where('mm_repaire_record.delete','<',ModelUtil::getDeleteLevel())
		->count();
	}
}