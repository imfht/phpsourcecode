<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\TreeTrunk;
use App\Util\TreeUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Util\ModelUtil;

class TreeTrunkController extends Controller
{
	
	public function store(Request $request){

		// 数据验证
		$this->validate($request, [
				'type' => 'required', // 必填、在 articles 表中唯一、最大长度 255
				'number' => 'required', // 必填
		]);
		
		$user = Auth::user();// $user 一定存在，因为当前方法在 路由中设为，需要经过 Auth 系统验证
		// 确保部门编号唯一
		$trunks = TreeTrunk::where('company_id',$user->company_id)
		->where('number',$request->get('number'))
		->get();
		if($trunks->count()>0){
			return $this->jsonResult(1,[],'部门编号重复，请使用新的编号');
		}
		//更新数据方法
		$isUpdate = $request->get('saveOrUpdate');
		if( $isUpdate == 'update'){
			return $this->update($request,$user->company_id);
		}
		
		
		
		$treeTrunk = new TreeTrunk();
		$treeTrunk->name = $request->get('name');
		$treeTrunk->type = $request->get('type');
		$treeTrunk->number = $request->get('number');
		$treeTrunk->description = $request->get('description');
		$treeTrunk->parent_id = TreeUtil::nodeidToRecordid($request->get('parentId'));
		$treeTrunk->company_id = $user->company_id;
		
		// 将数据保存到数据库，通过判断保存结果，控制页面进行不同跳转
		if ($treeTrunk->save()) {
			$treeTrunk = TreeTrunk::where('name',$treeTrunk->name)
			->where('company_id',$treeTrunk->company_id)
			->where('parent_id',$treeTrunk->parent_id)
			->where('delete','<',ModelUtil::getDeleteLevel())
			->first();
			
			if(empty($treeTrunk) == false){
				return $this->jsonResult(0,
						['id' => TreeUtil::recordidToNodeid($treeTrunk->id,TreeUtil::TRUNK), 
						'parent_id' => TreeUtil::recordidToNodeid($treeTrunk->parent_id,TreeUtil::TRUNK),
						'type' => 'trunk',
						'name' => $treeTrunk->name]);
			}
		}
			// 保存失败，跳回来路页面，保留用户的输入，并给出提示
		return $this->jsonResult(1, [],'保存失败');
		
		
	}
	public function update(Request $request,$companyId){
		// 数据验证
		$this->validate($request, [
				'id' => 'required', 
				'type' => 'required', 
				'number' => 'required', 
				'name' => 'required',
				'description' => 'required', 
		]);
		$nodeId = TreeUtil::nodeidToRecordid($request->get('id'));
		$result = TreeTrunk::where('id',$nodeId)
		->update(['name' =>$request->get('name'),'type'=>$request->get('type'),
				'number' =>$request->get('number'),'description' => $request->get('description')
		]);
		// 将数据保存到数据库，通过判断保存结果，控制页面进行不同跳转
		if ($result) {
			
			$treeTrunk = $treeTrunk = TreeTrunk::findOrFail($nodeId);
			if(empty($treeTrunk) == false){
			return $this->jsonResult(0,
					['id' => TreeUtil::recordidToNodeid($treeTrunk->id,TreeUtil::TRUNK),
					'parent_id' => TreeUtil::recordidToNodeid($treeTrunk->parent_id,TreeUtil::TRUNK), 
					'type' => 'trunk',
					'name' => $treeTrunk->name]);
			}
		} else {
			
			// 保存失败，跳回来路页面，保留用户的输入，并给出提示
			return $this->jsonResult(1, [],'更新失败-aa');
		}
	}
}