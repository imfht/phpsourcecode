<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Material;
use App\Config;
use Illuminate\Support\Facades\Auth;
use App\Util\StringUtil;
use App\UsingRecord;
use App\Deliver;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use App\Util\DateUtil;
use App\Util\PaginateUtil;
use App\Appointment;
use iscms\Alisms\SendsmsPusher as Sms;
use App\Util\SMSUtil;
use App\Http\Controllers\Controller;
use App\User;
use App\Util\ModelUtil;
class RentController extends Controller{
	

	/*
	 * 获取租借物资的信息表
	 */
	public function getRent(Request $request, $materialId) {
		$material = Material::findOrFail ( $materialId );
		$deadline = time();
		$user = Auth::user();
		if(!is_array($user->address)){
			$user->address = StringUtil::recordToRequestAddress($user->address);
		}
		return view('elements/materialTable',['material'=>$material,
				'place'=>'RentMaterial','url'=>$materialId,
				'deadline'=>date('Y-m-d',strtotime('+'.DateUtil::RENT_MAX_TIME_DAYS.' '.DateUtil::RENT_TIME_TYPE)),
				'user'=>$user,
		]);// url 使用的是相对 url ,指当前 url 路径下。与文件的相对路径类似
	}
	/*
	 * 提交物资租借信息
	 */
	public function postRent(Request $request,$materialId){
		// 数据验证
		$this->validate ( $request, [
				'materialId' => 'required', // 必填
				'time' => 'required'  // 必填
		] );
		$material = Material::findOrFail($materialId);
		if(empty($material)){
			abort(500);
		}
		$user = Auth::user();
		$rentRecord = new UsingRecord();
		$rentRecord->material_id = $materialId;
		$rentRecord->description = '';
		$rentRecord->deadline = $request->get('time');
		$rentRecord->startTime = date(DateUtil::FORMAT,time());
		$rentRecord->user_id = $user->id;
		$rentRecord->tree_trunk_id = $user->tree_trunk_id;
		$rentRecord->company_id = $user->company_id;
		$rentRecord->delete = ModelUtil::DELETE_NORMAL;
		$rentRecord->has_deliver = UsingRecord::HAS_DELIVER_NO;// 默认
		$has_deliver = $request->get('deliver');
		if($has_deliver == 'yes'){
			$rentRecord->has_deliver = UsingRecord::HAS_DELIVER_YES;
			$deliver = new Deliver();
			$deliver->phone = $request->get('phone');
			$province = $request->get('province');
			$city = $request->get('city');
			$district = $request->get('district');
			$detailAddress = $request->get('detailAddress');
			$deliver->address = StringUtil::requestAddressToRecord($province, $city, $district, $detailAddress);
		}
	
	
		if($rentRecord->save()){
			// 改变物资状态信息
			$material->status = Material::MATERIAL_STATUS_WASRENT;
			// 如果有送货上门服务
			if($has_deliver == 'yes'){
				$rentRecord = UsingRecord::where('user_id',$rentRecord->user_id)
				->where('material_id',$rentRecord->material_id)
				->where('startTime',$rentRecord->startTime)
				->first();
				if(empty($rentRecord)){
					abort(500);
				}
				$deliver->using_record_id = $rentRecord->id;
				$deliver->status = Deliver::STATUS_ORDER;
				$deliver->accepter_name = $user->name;
				$deliver->save();
				// 改变物资状态信息
				//$material->status = Material::MATERIAL_STATUS_WAIT_DELIVER;
			}
			// 保存改变状态后的物资信息
			$material->save();
			return Redirect::to('/admin/material/rent/history/person/unreturn');
			//
		}
		return redirect()->back()->withInput()->withErrors('提交信息失败！');
	}
	/**
	 * 
	 * @param unknown $where	表示个人身份还是管理员的身份（管理员也可以以普通人的身份去租借）
	 * @param unknown $type		那种类型的历史记录，与表的 status 有关
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function showRentHistory($where,$type){
		$params = $this->getHistoryParametersArray($where, $type);
		$usingRecords = UsingRecord::getRecords($params[0],$params[1],
				$params[2],$params[3],$params[4],$params[5],$params[6]);		
		return $this->getHistoryView($usingRecords, $where, $type);
	}
	/**
	 * 
	 */
	public function showHistorySearch(Request $request,$where,$type){
		
		$searchParams = ModelUtil::getHistorySearchParams($request, 'mm_material');
		$params = $this->getHistoryParametersArray($where, $type);
		$usingRecords = UsingRecord::getSearchRecordsResult(
				$searchParams[0], $searchParams[1],
				$params[0],$params[1],$params[2],$params[3],
				$params[4],$params[5],$params[6]);
		return $this->getHistoryView($usingRecords, $where, $type);
	}
	/**
	 *	获取历史记录跳转的页面
	 */
	private function getHistoryView($records,$where,$type){
		return view('MaterialLifecycle/rentRecord',
				['usingRecords'=>$records,
						'where'=>$where,'type'=>$type]);
	}
	/**
	 * 获取历史记录所需要的参数
	 */
	private function getHistoryParametersArray($where,$type){
		$user = Auth::user();
		$column ='';
		$method = 'where';
		$value = '';
		$delete = ModelUtil::DELETE_SELF;
		switch ($where){
			case ModelUtil::WHERE_PERSON :
				// 如果是个人，那么根据记录的用户 id
				$column = 'mm_using_record.user_id';
				$method = 'where';
				$value = $user->id;
				$delete = ModelUtil::DELETE_SELF;
				break;
			case ModelUtil::WHERE_MANAGE :
				if ($user->tree_trunk_id == 0){
					// 如果是超级管理员，需要根据员工查询记录，不可以根据物资查询，因为有记录的物资可能被删除
					$column = 'mm_using_record.company_id';
					$method = 'where';
					$value = $user->company_id;
					$delete = ModelUtil::DELETE_MANAGER_SUPER;
				}else{
					// 如果是部门管理员，那么只能查询属于本部门物资的记录
					$column = 'mm_material.tree_trunk_id';
					$method = 'whereIn';
					//$value = array(0);
 					$value = ModelUtil::getUserSubtrunk($user->tree_trunk_id,true); 
					$delete = ModelUtil::DELETE_MANAGER_DEPARTMENT;
				}
				break;
			default:
				abort(404,'没有此类型的租用历史');
		}
		
		$time = date(DateUtil::FORMAT);
		$timeOperator = '>';
		switch ($type){
			case 'unreturn':
				$time = date(DateUtil::FORMAT);
				$timeOperator = '>';
				break;
			case 'all':
				$time = '';
				$timeOperator = '!=';
				break;
			default:
				abort(404,'没有此类型的物资递送的历史记录');
		}
		return array($user->company_id,$column,
				$method, $value, $time, $timeOperator,  $delete);
	}
	/*
	 * 归还物资动作
	 */
	public function returnMaterial(Sms $sms,$rentRecordId){
		$user = Auth::user();
		$rentRecord = UsingRecord::findOrFail($rentRecordId);
		if($rentRecord->company_id != $user->company_id){
			abort(403,'非本公司的人不人执行归还操作');
		}
		$rentUser = User::findOrFail($rentRecord->user_id);
		if(($rentRecord->user_id != $user->id &&
		!(ModelUtil::isUserInCurrentUserSubTree($user->tree_trunk_id, $rentUser->tree_trunk_id)))){
			abort(403,'非本人或管理员，不能执行归还操作');
		}
		$material = Material::findOrFail($rentRecord->material_id);
		$material->status = Material::MATERIAL_STATUS_AVAILABLE;
		$material->save();
		
		$rentRecord->has_deliver = UsingRecord::HAS_DELIVER_RETURNED;
		$rentRecord->deadline = Date(DateUtil::FORMAT);
		$rentRecord->save();
		// 如果有人预约，则发送短信通知他们，当前资源可用
		$bookedPhones = DB::table('mm_appointments as a')
		->leftjoin('users','users.id','=','a.user_id')
		->select('users.phone')
		->where('a.material_id',$material->id)
		->where('a.status',Appointment::APPOINTMENT_STATUS_APPOINTED)
		->get();
		if($bookedPhones->count()>0){
			$phones = [];
			foreach($bookedPhones as $bookedPhone){
				$phones[] = $bookedPhone->phone;
			}
			$result = SMSUtil::sendAppointmentAvailable($sms, $phones, $material->name);
		}
		return $this->jsonResult(0);
	}
}