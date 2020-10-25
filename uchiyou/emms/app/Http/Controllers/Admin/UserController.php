<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Util\StringUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Util\ModelUtil;

class UserController extends Controller{
	
	// 用户个人中心主页
	public function index(){
		//获取已认证的用户信息
		$user = Auth::user();
		if(!is_array($user->address)){
			$user->address = StringUtil::recordToRequestAddress($user->address);
		}
		return view('user/user',['user' => $user]);
	}
	
	/*
	 * 用户自己更新用户的基本信息，自己不能修改工号
	 */
	public function updateBaseInfo(Request $request){
		$this->validate($request, [
				'province' => 'required',
				'city' => 'required',
				'district' => 'required',
				'name' => 'required',
				'email' => 'required',
				'phone' => 'required',
		]);
		$province = $request->get('province');
		$city = $request->get('city');
		$district = $request->get('district');
		$detailAddress = $request->get('detailAddress');
		$address = StringUtil::requestAddressToRecord($province, $city, $district, $detailAddress);
		
		$name =$request->get('name');
		$phone = $request->get('phone');
		$email = $request->get('email');
		$user = Auth::user();
		$user->name = $name;
		$user->phone = $phone;
		$user->email = $email;
		User::where('id',$user->id)
		->update(['name' => $name,'email' => $email,
				'address' => $address,'phone' => $phone,
		]);
		return $this->jsonResult(0,[],'更新成功');
	}
	/*
	 *用户自己 更新用户密码
	 */
	public function updatePassword(Request $request){
		$oldPassword = $request->get('oldPassword');
		$newPassword = $request->get('newPassword');
		$msg = "更新密码成功";
		$user = Auth::user();
		if (!Hash::check($oldPassword, $user->password)) {
			// 密码对比...
			$msg = "原密码输入错误";
			return $this->jsonResult(1,[],$msg);
		}
		User::where('id',$user->id)
		->update(['password' =>bcrypt($newPassword)]);
		$user->password = bcrypt($newPassword);
		return $this->jsonResult(0,[],$msg);
	}
	/*
	 * 删除各种类型的记录
	 */
	public function delete($recordType,$recordId,$where){
		$user = Auth::user();
		
		$table ='\\App\\';
		switch ($recordType){
			case 'rent':
				$table = $table.'UsingRecord';
				break;
			case 'deliver':
				$table = $table.'Deliver';
				break;
			case 'appointment':
				$table = $table.'Appointment';
				break;
			case 'purchase':
				$table = $table.'ApplyRecord';
				break;
			case 'repaire':
				$table = $table.'RepaireRecord';
				break;
			case 'user':
				$table = $table.'User';
				break;
			case 'material':
				$table = $table.'Material';
				break;
			case 'trunk':
				$table = $table.'TreeTrunk';
				break;
			default:
				abort(404,'没有这种类型的删除操作');
		}
		
		$record = $table::findOrFail($recordId);
		$delete = ModelUtil::DELETE_SELF;
		switch ($where){
			case ModelUtil::WHERE_PERSON :
				$delete = ModelUtil::DELETE_SELF;
				break;
			case ModelUtil::WHERE_MANAGE :
				if($user->job_type == User::USER_JOB_MANAGER){
					if($user->tree_trunk_id == 0){
						$delete = ModelUtil::DELETE_MANAGER_SUPER;
					}else{
						$delete = ModelUtil::DELETE_MANAGER_DEPARTMENT;
					}
					break;
				}
			default:
				abort(404,'这种角色不可以删除记录');
		}
		$record->delete = $delete;
		if($record->save()){
			return $this->jsonResult(0,[],'操作成功');
		}
		return $this->jsonResult(1,[],'保存记录失败');
	}
}