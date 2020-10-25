<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Util\TreeUtil;

class EmployeeController extends Controller{
	/*
	 * 管理员创建员工信息
	 */
	public function store(Request $request){
		// 数据验证
		$this->validate($request, [
				'name' => 'required', // 必填
				'email' => 'required', // 必填
				'password' => 'required', // 必填
		]);
		$curUser = Auth::user();
		// 确保员工在一家公司内，编号唯一
		if($request->has('number')){
			$users = User::where('company_id',$curUser->company_id)
			->where('number',$request->get('number'))
			->get();
			if($users->count()>0){
				return $this->jsonResult(1,[],'员工编号已经存在，请使用新的编号');
			}
		}
		$user = new User();
		//更新数据方法
		if($request->get('saveOrUpdate') == 'update'){
			//return $this->administorUpdate($request,$user->company_id);
			$nodeId =$request->get('id');
			if(empty($nodeId) === false){
				$nodeId =  TreeUtil::nodeidToRecordid($nodeId);
				$user = User::findOrFail($nodeId);
			}
		}
		$user->name = $request->get('name');
		$user->password = bcrypt( $request->get('password'));
		$user->email = $request->get('email');
		$user->number = $request->get('number');
		$user->job_type = $request->get('jobType');
		$user->sort = 0;
		$user->tree_trunk_id = TreeUtil::nodeidToRecordid($request->get('parentId'));
		$user->company_id = $curUser->company_id;
		// 将数据保存到数据库，通过判断保存结果，控制页面进行不同跳转
		if ($user->save()) {
			$user = User::where('name',$user->name)
			->where('company_id',$user->company_id)
			->where('email',$user->email)->first();
	
			if(empty($user) == false){
				return $this->jsonResult(0, ['id' => TreeUtil::recordidToNodeid($user->id, TreeUtil::LEAF),
						'parent_id' => TreeUtil::recordidToNodeid($user->tree_trunk_id,TreeUtil::TRUNK),
						'type' => 'user',
						'name' => $user->name]);
			}
		}
		// 保存失败，跳回来路页面，保留用户的输入，并给出提示
		return $this->jsonResult(1, [],'保存失败');
	}
	
	/*
	 * 管理员修改员工信息，可以修改员工的所有信息
	 */
	public function administorUpdate(Request $request,$companyId ){
		$nodeId =  TreeUtil::nodeidToRecordid($request->get('id'));
		$result = User::where('id',$nodeId)
		->update(['name' =>$request->get('name'),'password' =>bcrypt($request->get('password')),
				'email' =>$request->get('email'),'number' => $request->get('number'),
				'job_type' => $request->get('jobType'),
		]);
		// 将数据保存到数据库，通过判断保存结果，控制页面进行不同跳转
		if ($result) {
			$user = User::where('id',$nodeId)->first();
			if(empty($user) == false){
				return $this->jsonResult(0, ['id' => TreeUtil::recordidToNodeid($nodeId, TreeUtil::LEAF),
						'parent_id' => TreeUtil::recordidToNodeid($user->tree_trunk_id,TreeUtil::TRUNK),
						'type' => 'user',
						'name' => $user->name]);
			}
		} else {
			// 保存失败，跳回来路页面，保留用户的输入，并给出提示
			return $this->jsonResult(1, [],'更新信息失败');
		}
	}
}