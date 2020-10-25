<?php

namespace App\Http\Controllers\Admin\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Company;
use App\User;
use App\Util\TreeUtil;
use App\TreeTrunk;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller{

	public function getChildren(Request $request,$parentId){
		$user = Auth::user();// $user 一定存在，因为当前方法在 路由中设为，需要经过 Auth 系统验证
		$jsonArray = Company::getCompanyOrganizationTreeArray( $user->company_id,$user->tree_trunk_id, $parentId);
		return json_encode($jsonArray,JSON_UNESCAPED_UNICODE);
	}
	
	public function getANode($nodeId){
		$user = Auth::user();// $user 一定存在，因为当前方法在 路由中设为，需要经过 Auth 系统验证
		$jsonArray = Company::getAOrganizationNodeData($user->company_id, $nodeId);
		return json_encode($jsonArray,JSON_UNESCAPED_UNICODE);
	}
	public function getANodeDataTable(Request $request,$nodeId){
		$user = Auth::user();// $user 一定存在，因为当前方法在 路由中设为，需要经过 Auth 系统验证
		$node = Company::getANodeDataModel($user->company_id, $nodeId,
				TreeUtil::TYPE_ORGANIZATION_TRUNK);
		if(TreeUtil::LEAF == TreeUtil::getNodeType($nodeId)){
			return view('elements/employee',[
					'user'=>$node,
					'place'=>'showNodeInfo',
			]);
		}else{
			return view('elements/treeTrunkTable',['directory'=>$node,
					'place'=>'showNodeInfo']);
		}
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
						User::where('id','=',$nodeId)->update($data);
					}else{
						$data = ['parent_id'=>$parentId,'sort'=>$item['sort']];
						TreeTrunk::where('id','=',$nodeId)->update($data);
					}
				}
			}
		}
		return $this->jsonResult(0);
	}
	/*
	 * 删除一个节点,以及它的子节点
	 */
	public function deleteNode($nodeId){
		if(empty($nodeId)){
			abort(404);
		}
		// 待验证权限
		Company::deleteNode($nodeId,TreeUtil::TYPE_ORGANIZATION_TRUNK);
		return $this->jsonResult(0,[],'sucess');
	}
	/**
	 * 判断当前节点是不是自己
	 * @param unknown $nodeId
	 * @return true - 是自己的节点； false -  不是自己的节点
	 */
	public function isSelfNode($nodeId){
		if(TreeUtil::TRUNK == TreeUtil::getNodeType($nodeId)){
			return $this->jsonResult(0,0,'false');
		}
		$user = Auth::user();// $user 一定存在，因为当前方法在 路由中设为，需要经过 Auth 系统验证
		$nodeID = TreeUtil::nodeidToRecordid($nodeId);
		if($user->id == $nodeID){
			return $this->jsonResult(0,1,'true');
		}else{
			return $this->jsonResult(0,0,'false');
		}
	}
}