<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\ApplyRecord;
use Illuminate\Support\Facades\Auth;
use App\Util\PaginateUtil;
use Illuminate\Support\Facades\DB;
use App\Message;
use App\Util\MessageUtil;
use App\Util\ModelUtil;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Util\SessionUtil;
use App\Material;

class PurchaseController extends Controller {
	public function purchaseApply(Request $request) {
		// 数据验证
		$this->validate ( $request, [ 
				'type' => 'required', // 必填
				'name' => 'required'  // 必填
		] );
		$user = Auth::user ();
		
		$applyRecord = new ApplyRecord ();
		$applyRecord->company_id = $user->company_id;
		$applyRecord->tree_trunk_id = $user->tree_trunk_id;
		$applyRecord->user_id = $user->id;
		$applyRecord->statuses = ApplyRecord::APPLYRECORD_STATUS_APPLY;
		$applyRecord->name = $request->get ( 'name' );
		$applyRecord->main_type = $request->get ( 'mainType' );
		$applyRecord->type = $request->get ( 'type' );
		$applyRecord->price = $request->get ( 'price' );
		$applyRecord->quantity = $request->get ( 'quantity' );
		//$applyRecord->approvers = $request->get ( 'approver' );
		$applyRecord->description = $request->get ( 'description' );
		$applyRecord->apply_type = ApplyRecord::APPLYRECORD_TYPE_PURCHASE;
		if ($applyRecord->save ()) {
			return $this->jsonResult ( 0 ,[],'申请成功');
		}
		abort(500,'系统错误,申请购买失败');
	}
	
	/*
	 * 产生通知消息
	 */
/* 	private function saveApplyMessage(User $user, ApplyRecord $applyRecord) {
		// 保存通知消息
		$treeTrunkName = ""; // 部门名称
		$treeTrunk = TreeTrunk::where ( 'id', $user->tree_trunk_id )->first ();
		if (empty ( $treeTrunk ) == false) {
			$treeTrunkName = $treeTrunk->name;
		} else {
			$treeTrunkName = session ( SessionUtil::COMPANY )->name;
			; // 如果是顶层用户，则部门名称为公司名称
		}
		$msg = MessageUtil::getPurchaseApplyMessage ( $applyRecord, $treeTrunkName, $user );
		$approversArray = StringUtil::applyRecordApproversToArray ( $applyRecord->approvers );
		for($index = 0; $index < count ( $approversArray ); ++ $index) {
			$approver = User::where ( 'company_id', $user->company_id )->where ( 'name',
			 $approversArray [$index] )->first ();
			if (empty ( $approver ) == false) {
				Message::saveMessage ( $user->id, $approver->id, $msg, 
				MessageUtil::EVENT_PURCHASEAPPLY, $applyRecord->id );
			}
		}
		return $this->jsonResult ( 0, [ ], '申请成功' );
	} */
	private function getHistoryParametersArray($where, $type) {
		$user = Auth::user ();
		$delete = ModelUtil::DELETE_SELF;
		
		$column = '';
		$method = 'where';
		$value = '';
		switch ($where) {
			case ModelUtil::WHERE_PERSON :
				$column = 'mm_apply_records.user_id';
				$value = $user->id;
				$method = 'where';
				$delete = ModelUtil::DELETE_SELF;
				break;
			case ModelUtil::WHERE_MANAGE :
				if ($user->tree_trunk_id == 0) {
					$column = 'mm_apply_records.company_id';
					$value = $user->company_id;
					$method = 'where';
					$delete = ModelUtil::DELETE_MANAGER_SUPER;
				} else {
					$column = 'users.tree_trunk_id';
 					$value = ModelUtil::getUserSubtrunk($user->tree_trunk_id,true);
					$method = 'whereIn';
					$delete = ModelUtil::DELETE_MANAGER_DEPARTMENT;
				}
				break;
			default :
				abort ( 404, '不存在这种类型的购买申请记录' );
		}
		
		$statuses = ApplyRecord::APPLYRECORD_STATUS_APPLY;
		$statusOperator = '=';
		switch ($type) {
			case 'apply' :
				$statuses = ApplyRecord::APPLYRECORD_STATUS_APPLY;
				$statusOperator = '=';
				break;
			case 'agree' :
				$statuses = ApplyRecord::APPLYRECORD_STATUS_AGREE;
				$statusOperator = '=';
				break;
			case 'receive' :
				$statuses = ApplyRecord::APPLYRECORD_STATUS_ALLOCATION;
				$statusOperator = '=';
				break;
			case 'all' :
				$statuses = ApplyRecord::APPLYRECORD_STATUS_DELETED;
				$statusOperator = '<';
				break;
			default :
				abort ( 404, '不存在这种类型的购买申请记录' );
		}
		return array (
				$user->company_id,
				$column,
				$method,
				$value,
				$statuses,
				$statusOperator,
				$delete 
		);
	}
	public function showPurchaseHistory($where, $type) {
		$params = $this->getHistoryParametersArray ( $where, $type );
		$applys = ApplyRecord::getRecord ( $params [0], $params [1], 
				$params [2], $params [3], $params [4], $params [5], $params [6] );
		return $this->getHistoryView ( $applys, $where, $type );
	}
	private function getHistoryView($records, $where, $type) {
		return view ( 'MaterialLifecycle/showPurchase', [ 
				'purchaseRecords' => $records,
				'where' => $where,
				'type' => $type 
		] );
	}
	public function showHistorySearch(Request $request,$where,$type){
	
		$searchContent = $request->get('content');
		$searchType = $request->get('type');
		$column = '';
		switch ($searchType) {
			// 不一定是 'mm_material ' 表
			case 'materialName' :
				$column = 'mm_apply_records.name';
				break;
				// 用户名，一定是 'users' 表
			case 'userName' :
				$column = 'users.name';
				break;
			default :
				abort ( 404, '不存在这种类型的历史记录搜索' );
		}
		$params = $this->getHistoryParametersArray($where, $type);
		$records = ApplyRecord::getSearchRecordsResult(
				$column, $searchContent,
				$params[0],$params[1],$params[2],$params[3],
				$params[4],$params[5],$params[6]);
		return $this->getHistoryView($records, $where, $type);
	}
	public function exportExcel($where,$type){
		$excelName = 'error';
		switch ($type) {
			case 'apply' :
				$excelName = '待审批购买记录';
				break;
			case 'agree' :
				$excelName = '已同意待分配的购买申请记录';
				break;
			case 'receive' :
				$excelName = '已分配待接收的购买申请记录';
				break;
			case 'all' :
				$excelName = '所有购买申请记录';
				break;
			default :
				abort ( 404, '不存在这种类型的购买申请记录' );
		}
		$params = $this->getHistoryParametersArray ( $where, $type );
		$applys = ApplyRecord::getAllRecord ( $params [0], $params [1],
				$params [2], $params [3], $params [4], $params [5], $params [6] );
		$cellData = [['物资名称','申请人','电话','员工编号','所在部门',
				'资产类型','类别','数量','申请时间','状态']];
		foreach($applys as $apply){
			$departmentName = session(SessionUtil::COMPANY)->name;
			if(empty($apply->departmentNumber)==false){
				$departmentName = $apply->departmentNumber;
			}
			$employeeNumber = '暂无';
			if(empty($apply->employeeNumber) == false){
				$employeeNumber = $apply->employeeNumber;
			}
			$assetType = '固定资产';
			if($apply->main_type == Material::MATERIAL_MAIN_TYPE_NOT_FIXED_ASSET){
				$assetType = '耗材';
			}
			$status = '待审批';
			switch ($apply->statuses){
				case 1:
					$status = '待审批';
					break;
				case 3:
					$status = '已同意';
					break;
				case 4:
					$status = '已拒绝';
					break;
				case 5:
					$status = '已分派';
					break;
				case 6:
					$status = '已接收';
					break;
				default:
					$status = '服务器数据错误';
			}
			$cellData[] = [$apply->name,$apply->userName,$apply->phone,
					$employeeNumber,
					$departmentName,
					$assetType,$apply->type,$apply->quantity,$apply->created_at,
					$status
			];
		}
		
		Excel::create($excelName,function($excel) use ($cellData){
			$excel->sheet('审批列表', function($sheet) use ($cellData){
				$sheet->rows($cellData);
			});
		})->export('xls');
	}
	
	public function doApprovePurchase($purchaseApplyId, $operate) {
		$apply = ApplyRecord::findOrFail ( $purchaseApplyId );
		$action = '同意';
		switch ($operate) {
			case 'agree' :
				$apply->statuses = ApplyRecord::APPLYRECORD_STATUS_AGREE;
				$action = '同意';
				break;
			case 'reject' :
				$apply->statuses = ApplyRecord::APPLYRECORD_STATUS_REJECT;
				$action = '拒绝';
				break;
			case 'allocation' :
				$apply->statuses = ApplyRecord::APPLYRECORD_STATUS_ALLOCATION;
				$action = '分派';
				break;
			case 'receive' :
				$apply->statuses = ApplyRecord::APPLYRECORD_STATUS_RECEIVED;
				$action = '收到';
				$apply->save ();
				return $this->jsonResult ( 0 );
			case 'delete' :
				$apply->statuses = ApplyRecord::APPLYRECORD_STATUS_DELETED;
				$action = '删除';
				$apply->save ();
				return $this->jsonResult ( 0 );
			default :
				return $this->jsonResult ( 1, [ ], 'not exist operate' );
		}
		$apply->save ();
		// 发送一条站内消息给申请者，通知结果
		$user = Auth::user ();
		$message = MessageUtil::getPurchaseApplyReply ( '在' . $apply->create_at . '购买' . $apply->name . '的申请', $apply->approvers, $action );
		Message::saveMessage ( $user->id, $apply->user_id, $message, MessageUtil::EVENT_READ_ONCE, '' );
		return $this->jsonResult ( 0, [ ], 'sucess' );
	}
	public function getWaitApproveCount(){
		$user = Auth::user();
		return DB::table('mm_apply_records')
		->where('company_id','=',$user->company_id)
		->whereIn('tree_trunk_id',ModelUtil::getUserSubtrunk($user->tree_trunk_id))
		->where('statuses','=',ApplyRecord::APPLYRECORD_STATUS_APPLY)
		->where('delete','<',ModelUtil::getDeleteLevel())
		->count();
	}
}