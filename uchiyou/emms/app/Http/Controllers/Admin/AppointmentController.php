<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Appointment;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Util\DateUtil;
use Illuminate\Support\Facades\DB;
use App\Util\PaginateUtil;
use App\Util\ModelUtil;
use Illuminate\Http\Request;
use App\User;

class AppointmentController extends Controller {
	
	/**
	 * 预约的动作， get 方式提交请求
	 */
	public function getAppointment($materialId) {
		$user = Auth::user ();
		$appointment = new Appointment ();
		$appointment->user_id = $user->id;
		$appointment->company_id = $user->company_id;
		$appointment->material_id = $materialId;
		$appointment->status = Appointment::APPOINTMENT_STATUS_APPOINTED;
		$appointment->start_time = date ( DateUtil::FORMAT );
		if ($appointment->save ()) {
			return Redirect::to ( '/admin/material/appointment/history/person/appointed' );
		}
		return redirect ()->back ()->withInput ()->withErrors ( '提交信息失败！' );
	}
	/**
	 * 取消预约的动作， get 方式的请求
	 */
	public function getDisappointment($recordId) {
		$user = Auth::user ();
		// 用户的 id 和 物资的 id 是唯一的
		$appointment = Appointment::findOrFail($recordId);
		if($appointment->delete > ModelUtil::getDeleteLevel()){
			abort(404,'该记录已经被删除了');
		}
		$appointMan = User::findOrFail($appointment->user_id);
		if ($user->id == $appointment->user_id ||
			ModelUtil::isUserInCurrentUserSubTree($user->tree_trunk_id, $appointMan->tree_trunk_id)) {
			$appointment->status = Appointment::APPOINTMENT_STATUS_CANCELED;
			$appointment->finish_time = date ( DateUtil::FORMAT );
			if ($appointment->save ()) {
				return $this->jsonResult(0,[],'sucess');
			}
		}
		abort(403,'您无权取消该预约');
	}
	/**
	 * 物资预约的记录表
	 * @param $where 显示历史记录的地方或范围，如 个人显示，管理员查看整个公司的记录等
	 * @param $type 显示历史记录的类型，如 只显示正在预约的记录还是全部的预约记录
	 */
	public function showAppointmentHistory($where, $type) {
		$params = $this->getHistoryParametersArray ( $where, $type );
		// mm_appointments 不要轻易的将表名称简写，注意和 $column 对应
		$appointmentRecord = Appointment::getRecord ( $params [0], $params [1], $params [2], $params [3], $params [4], $params [5], $params [6] );
		return $this->getHistoryView ( $appointmentRecord, $where,$type );
	}
	private function getHistoryView($records, $where,$type) {
		return view ( 'MaterialLifecycle/showAppointments', [ 
				'appointmentRecords' => $records,
				'where' => $where ,
				'type' => $type,
		] );
	}
	private function getHistoryParametersArray($where, $type) {
		$user = Auth::user ();
		$statusOperator = '=';
		$status = Appointment::APPOINTMENT_STATUS_APPOINTED;
		switch ($type) {
			case 'appointed' :
				$statusOperator = '=';
				$status = Appointment::APPOINTMENT_STATUS_APPOINTED;
				break;
			case 'all' :
				$statusOperator = '!=';
				$status = '';
				break;
			default :
				abort ( 404, '没有此类型的预约记录' );
		}
		$delete = ModelUtil::DELETE_SELF;
		$column = '';
		$value = '';
		$method = 'where';
		switch ($where) {
			case ModelUtil::WHERE_PERSON :
				$column = 'mm_appointments.user_id';
				$method = 'where';
				$value = $user->id;
				$delete = ModelUtil::DELETE_SELF;
				break;
			case ModelUtil::WHERE_MANAGE :
				if ($user->tree_trunk_id == 0) {
					$column = 'mm_appointments.company_id';
					$method = 'where';
					$value = $user->company_id;
					$delete = ModelUtil::DELETE_MANAGER_SUPER;
				} else {
					$column = 'mm_material.tree_trunk_id';
					$method = 'whereIn';
					$value = ModelUtil::getUserSubtrunk( $user->tree_trunk_id,true );
					$delete = ModelUtil::DELETE_MANAGER_DEPARTMENT;
				}
				break;
			default :
				abort ( 404, '没有此类型的租用历史' );
		}
		return array (
				$user->company_id,
				$column,
				$method,
				$value,
				$status,
				$statusOperator,
				$delete 
		);
	}
	public function showHistorySearch(Request $request,$where,$type){
	
		$searchParams = ModelUtil::getHistorySearchParams($request, 'mm_material');
		$params = $this->getHistoryParametersArray($where, $type);
		$records = Appointment::getSearchRecordsResult(
				$searchParams[0], $searchParams[1],
				$params[0],$params[1],$params[2],$params[3],
				$params[4],$params[5],$params[6]);
		return $this->getHistoryView($records, $where, $type);
	}
}