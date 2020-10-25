<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use iscms\Alisms\SendsmsPusher as Sms;
use Illuminate\Support\Facades\Auth;
use App\Deliver;
use App\UsingRecord;
use App\Util\MessageUtil;
use App\Message;
use App\Util\SMSUtil;
use Illuminate\Support\Facades\DB;
use App\User;
use App\TreeTrunk;
use App\Util\PaginateUtil;
use App\Util\ModelUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class DeliverController extends Controller{
	/*
	 * 开始递送物资
	 */
	public function startDeliver(Sms $sms,$deliverId){
		$user = Auth::user();
		$deliver = Deliver::findOrFail($deliverId);
		$deliver->status = Deliver::STATUS_DELIVERING;
		$deliver->deliver_man_id = $user->id;
		if($deliver->save()){
			$usingRecord = UsingRecord::findOrFail($deliver->using_record_id);
			$usingRecord->has_deliver = UsingRecord::HAS_DELIVER_DELIVERED;
			$usingRecord->save();
			// 给下订单的人一条通知
			$message = MessageUtil::getStartDeliverMessage($usingRecord);
			$user = Auth::user();
			Message::saveMessage($user->id, $usingRecord->user_id,
					$message, MessageUtil::EVENT_READ_ONCE, '');
			SMSUtil::sendDeliver($sms, $deliver->phone, $deliver->address);
		}
		return 'success';
	}
	/*
	 * 确认接收递送物资
	 * 使用事务处理 deliver 表和 using_record 表
	 */
	public function acceptDeliver($usingRecordId){
		$deliverRecord = DB::table('mm_using_record as u')
		->leftJoin('mm_delivers as d','d.using_record_id','=','u.id')
		->leftJoin('mm_material as m','m.id','=','u.material_id')
		->select('u.*','d.deliver_man_id as deliverMan',
				'm.material_number as material_number','m.name as material_name')
		->where('u.id',$usingRecordId)
		->first();
		if(empty($deliverRecord)){
			abort(404);
		}
				$user = Auth::user();
				$message = MessageUtil::getAcceptedDeliverMessage($deliverRecord, $user->name);
				// 保存的事务操作
				DB::beginTransaction();
				try{
					$deliverResult = DB::update('update mm_delivers set status = ? where using_record_id = ?',
							[Deliver::STATUS_ACCEPTED,$usingRecordId]);
					$usingRecordResult = DB::update('update mm_using_record set has_deliver = ? where id = ?',
							[UsingRecord::HAS_DELIVER_ACCEPTED,$usingRecordId]);
					if(!($deliverResult && $usingRecordResult)){
						throw new \Exception();
					}
					// 为递送人员发送一条成功接收的消息
					Message::saveMessage( $user->id,$deliverRecord->deliverMan, $message, MessageUtil::EVENT_READ_ONCE,'');
					DB::commit();
					//return $this->jsonResult(0,[],'acceptDeliver');
					return Redirect::to('/admin/material/rent/history/person/unreturn');
						
				} catch (\Exception $e){
					DB::rollback();
					abort(500);
				}
	}
	private function getHistoryParametersArray($type){
		$user = Auth::user();
		$statusOperator = '=';
		$status = Deliver::STATUS_ORDER;
		switch ($type){
			case 'orders':
				$statusOperator = '=';
				$status = Deliver::STATUS_ORDER;
				break;
			case 'all':
				$statusOperator = '!=';
				$status = '';
				break;
			default:
				abort(404,'没有此类型的物资递送的历史记录');
		}
		
		// 权限分配
		$delete = ModelUtil::DELETE_MANAGER_DEPARTMENT;
		$column = 'mm_material.tree_trunk_id';
		$method = 'whereIn';
		$value = ModelUtil::getUserSubtrunk($user->tree_trunk_id,true);
		if($user->tree_trunk_id == 0){
			$column = 'mm_using_record.company_id';
			$method= 'where';
			$value = $user->company_id;
			$delete = ModelUtil::DELETE_MANAGER_SUPER;
		}
		return array($user->company_id, $status,
					$statusOperator, $column, $method, 
					$value, $delete);
	}
	/**
	 * 显示待递送的物资信息
	 */
	public function showDeliverHistory($type){
		$params = $this->getHistoryParametersArray($type);
			$delivers = Deliver::getRecord($params[0],$params[1],
				$params[2],$params[3],$params[4],$params[5],$params[6]);
		return $this->getHistoryView($delivers,$type);
	}
	private function getHistoryView($records,$type){
		return view('MaterialLifecycle/showWaitDeliver',
				['delivers'=>$records,
					'type' => $type,
				]);
	}
	public function showHistorySearch(Request $request,$type){
	
		$searchContent = $request->get('content');
		$searchType = $request->get('type');
		$column = '';
		switch ($searchType) {
			// 不一定是 'mm_material ' 表
			case 'materialName' :
				$column = 'mm_material.name';
				break;
				// 用户名，一定是 'users' 表
			case 'userName' :
				$column = 'mm_delivers.accepter_name';
				break;
			default :
				abort ( 404, '不存在这种类型的历史记录搜索' );
		}
		
		$params = $this->getHistoryParametersArray($type);
		$records = Deliver::getSearchRecordsResult(
				$column, $searchContent,
				$params[0],$params[1],$params[2],$params[3],
				$params[4],$params[5],$params[6]);
		return $this->getHistoryView($records,$type);
	}
	public function getWaitDeliverCount(){
		$user = Auth::user();
		return DB::table('mm_delivers as d')
		->leftJoin('mm_using_record as u','u.id','=','d.using_record_id')
		->leftJoin('mm_material as m','m.id','=','u.material_id')
		->where('u.company_id','=',$user->company_id)
		->where('d.status',Deliver::STATUS_ORDER)
		->whereIn('m.tree_trunk_id',ModelUtil::getUserSubtrunk($user->tree_trunk_id))
		->where('d.delete','<',ModelUtil::getDeleteLevel())
		->count();
	}
}