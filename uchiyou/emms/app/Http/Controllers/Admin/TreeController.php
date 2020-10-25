<?php

namespace App\Http\Controllers\Admin;

use App\Appointment;
use App\Company;
use App\Http\Controllers\Controller;
use App\Material;
use App\TreeTrunk;
use App\Util\TreeUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\UsingRecord;
use App\User;
use App\Util\SessionUtil;
use App\Util\ModelUtil;

class TreeController extends Controller
{
	
	/*
	 * 获取一个json 格式的节点的信息
	 */
	public function getANodeData(Request $request,$nodeId){	
		$user = Auth::user();// $user 一定存在，因为当前方法在 路由中设为，需要经过 Auth 系统验证
		$jsonArray = Company::getANodeData($user->company_id, $nodeId);
		return json_encode($jsonArray,JSON_UNESCAPED_UNICODE);
	}
	/*
	 * 获取一个节点的信息,
	 */
	public function getANodeDataTable(Request $request,$nodeId){		
		$user = Auth::user();// $user 一定存在，因为当前方法在 路由中设为，需要经过 Auth 系统验证
		$node = Company::getANodeDataModel($user->company_id, $nodeId,
				TreeUtil::TYPE_MATERIAL_TRUNK);
		if(TreeUtil::LEAF == TreeUtil::getNodeType($nodeId)){
			// 判断该物资被预约的情况
			$appoints = Appointment::where('material_id',$node->id)
			->where('status',Appointment::APPOINTMENT_STATUS_APPOINTED)
			->where('delete','<',ModelUtil::getDeleteLevel())
			->get();
			// 如果是自己预约了，则显示取消预约按钮
			$appointNumbers = 0;
			$hasAppoint = false;
			if(empty($appoints) === false && $appoints->count()>0){
				$appointNumbers = $appoints->count();
				$myAppoint = $appoints->filter(
						function ($item) use ($user) {
   							 return $item->user_id == $user->id;
											});
				if(empty($myAppoint) === false && $myAppoint->count()>0){
					$hasAppoint = true;
				}
			}
			// 如果是自己已经借了的，则不能够再预约.前提是物资是被租用状态。
			if($node->status == Material::MATERIAL_STATUS_WASRENT){
				$now = date('y-m-d h:i:s',time());
				$isSelfRent = UsingRecord::where('user_id',$user->id)
				->where('material_id','=',$node->id)
				->where('deadline','>',$now)
				->where('startTime','<',$now)
				->where('delete','<',ModelUtil::getDeleteLevel())
				->get();
				if(empty($isSelfRent) === false && $isSelfRent->count()>0){
					$node->status = Material::MATERIAL_STATUS_WASAPPOINTMENT_SELF;
				}
			}
			$isManager = $user->job_type == User::USER_JOB_MANAGER?true:false;
			return view('elements/materialTable',['material'=>$node,
					'place'=>'showNodeInfo','appointNumbers'=>$appointNumbers,
					'hasAppoint'=>$hasAppoint,'isManager'=>$isManager
			]);
		}else{
			return view('elements/treeTrunkTable',['directory'=>$node,
					'place'=>'showNodeInfo']);
		}
	}
	
	/*
	 * 根据公司 id 和 节点id, 返回 该节点所有的儿子节点的信息，
	 * 返回 json 格式的数据
	 */
	public function getTreeData(Request $request,$nodeId){
		
		$user = Auth::user();// $user 一定存在，因为当前方法在 路由中设为，需要经过 Auth 系统验证
		$jsonArray = Company::getCompanyMaterialTreeArray( $user->company_id,$user->tree_trunk_id, $nodeId);
		return json_encode($jsonArray,JSON_UNESCAPED_UNICODE);
	}
	
	/*
	 * 删除一个节点,以及它的子节点
	 */
	public function deleteNode(Request $request,$nodeId){
		
		if(empty($nodeId)){
			abort(404);
		}
		$user = Auth::user();
		if($user->job_type != User::USER_JOB_MANAGER){
			abort(403);
		}
		Company::deleteNode($nodeId,TreeUtil::TYPE_MATERIAL_TRUNK);
		return $this->jsonResult(0,[],'sucess');		
	}
	
	/*
	 * 当用户拖动树节点进行重新排序
	 */
	public function sort(Request $request){
		
		$params = $request->getContent();
		if(empty($params) === false){
			$params = json_decode($params,true);
			if(empty($params) === false){
				foreach ($params as $item){
					
					$nodeId = $item['id'];
					$parentId = $item['parent'];
					$nodeType = TreeUtil::getNodeType($nodeId);
					$nodeId = TreeUtil::nodeidToRecordid($nodeId);
					$parentId = TreeUtil::nodeidToRecordid($parentId);
					if($nodeType == TreeUtil::LEAF){
					$data = ['tree_trunk_id'=>$parentId,'sort'=>$item['sort']];
						Material::where('id','=',$nodeId)
						->update($data);
					}else{
						$data = ['parent_id'=>$parentId,'sort'=>$item['sort']];
						TreeTrunk::where('id','=',$nodeId)->update($data);
					}
				}
			}
		}
		return $this->jsonResult(0);
	}
}