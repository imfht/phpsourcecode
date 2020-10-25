<?php

namespace App;

use App\Material;
use App\TreeTrunk;
use App\User;
use App\Util\ModelUtil;
use App\Util\TreeUtil;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
//use Faker\Provider\Company;

class Company extends Model
{
	/**
	 * 与模型关联的数据表
	 *
	 * @var string
	 */
	protected $table = 'mm_company';
	
	
	/*
	 * 删除树形结构中的一个节点,
	 * 
	 * $leafType 表示分支节点的类型,如果是组织结构，则需要删除 子分支，物资节点，员工节点
	 * 如果是树干节点，则对应 tree_trunk_id 的type 字段。如果是树叶节点，则决定使用 Users 表还是 Material表
	 * @parem int $leafType 
	 * 
	 * @param int $companyId
	 * @param int $nodeId
     * @return array
	 */
	public static function deleteNode($nodeId,$leafType){
		if(empty($nodeId)){
			return [];
		}
		$user = Auth::user();
		// 前端 jstree 节点的 id 向数据库记录的转换
		$nodeType = TreeUtil::getNodeType($nodeId);
		$nodeId = TreeUtil::nodeidToRecordid($nodeId);
		if($nodeType == TreeUtil::LEAF){// 删除树叶节点，有两种树叶节点
			$result = false;
			if($leafType == TreeUtil::TYPE_MATERIAL_TRUNK){
				$result = ModelUtil::delete('Material', $nodeId);
			}else if($leafType == TreeUtil::TYPE_ORGANIZATION_TRUNK){
				$result = ModelUtil::delete('User', $nodeId);
			}
			return $result;
		}
		// 迭代的删除一个分支
		if($nodeType == TreeUtil::TRUNK){// 删除一个分支
			Company::deleteTrunk($nodeId, $leafType,ModelUtil::getDeleteLevel());
		}
		return true;
	}
	
	/*
	 * 递归的删除一个树枝下的所有东西
	 * 如果树枝表示一个组织结构的话，那么删除该组织结构下的子机构，物资分支，员工
	 * 如果树枝表示物资分类，那么只删除该分支下的子分支
	 * @param int $nodeId
	 */
	private static function deleteTrunk($nodeId,$trunkType,$delete){
		
		ModelUtil::delete('TreeTrunk', $nodeId);
		// 如果该节点下有员工的话，删除该员工信息
		if($trunkType == TreeUtil::TYPE_ORGANIZATION_TRUNK){
			User::where('tree_trunk_id',$nodeId)
			->where('delete','<',ModelUtil::getDeleteLevel())//防止部门管理员的删除操作覆盖超级管理员
			->update(['delete'=>$delete]);
		}
		Material::where('tree_trunk_id',$nodeId)
		->where('delete','<',ModelUtil::getDeleteLevel())
		->update(['delete'=>$delete]);
		
		$trunks = TreeTrunk::where('parent_id','=',$nodeId)
		->where('delete','<',ModelUtil::getDeleteLevel())
		->get();
		if(empty($trunks)){
			return;
		}
		foreach ($trunks as $trunk) {
			Company::deleteTrunk($trunk->id,$trunk->type,$delete);//递归删除子树枝节点
		}
	}
	/*
	 * 获取公司物资管理的树形结构，获取 $nodeId 的所有子节点，包括树枝和树叶节点
	 * @param String $companyName
	 * @param int $companyId
	 * @param int $nodeId
     * @return array
	 */
	public static function getCompanyMaterialTreeArray ( $companyId,$treeTrunkId,$nodeId){
		
		if(empty($nodeId)){
			return [];
		}
		$nodeId = TreeUtil::nodeidToRecordid($nodeId);
		// 权限限制   ：普通用户只能看到自己所在部门的物资信息
		if($nodeId == 0){
			$nodeId = $treeTrunkId;
		}
		
		$jsonArray = [];
		$trunks = TreeTrunk::where('company_id','=',$companyId)
		->where('parent_id','=',$nodeId)
		->where('delete','<',ModelUtil::getDeleteLevel())
		->select(['id','name'])
		->orderBy('sort','ASC')
		->get();
		if(empty($trunks) === false){
			foreach ($trunks as &$item){

				$tmp['text'] = $item->name;
				$tmp['id'] = TreeUtil::recordidToNodeid($item ->id, TreeUtil::TRUNK);// 数据库记录的 id 转换为 jstree的 id
				$tmp['type'] = 'trunk';
				$tmp['children'] = true;
				$jsonArray[] = $tmp;// 高效的往数组中增加一个元素
			}
		}
		
		$leaves = Material::where('company_id','=',$companyId)
		->where('tree_trunk_id','=',$nodeId)
		->where('delete','<',ModelUtil::getDeleteLevel())
		->select(['id','name'])
		->orderBy('sort','ASC')
		->get();
		if(empty($leaves) === false){
			foreach ($leaves as &$item){
				$tmp['text'] = $item->name;
				$tmp['id'] = TreeUtil::recordidToNodeid($item ->id, TreeUtil::LEAF);
				$tmp['type'] = 'material';
				$tmp['children'] = false;
				$jsonArray[] = $tmp;// 高效的往数组中增加一个元素
			}
		}
		return $jsonArray;
	}
	/*
	 * 获取公司组织机构的树形结构，获取 $nodeId 的所有子节点，包括树枝和树叶节点
	 * @param String $companyName
	 * @param int $companyId
	 * @param int $nodeId
     * @return array
	 */
	public static function getCompanyOrganizationTreeArray ($companyId,$treeTrunkId,$nodeId){
		
		if(empty($nodeId)){
			return [];
		}
		$nodeId = TreeUtil::nodeidToRecordid($nodeId);
		// 权限限制   ：普通用户只能看到自己所在部门的物资信息
		if($nodeId == 0){
			$nodeId = $treeTrunkId;
		}
		
		$jsonArray = [];
		$trunks = TreeTrunk::where('company_id','=',$companyId)
		->where('parent_id','=',$nodeId)
		->where('type',TreeUtil::TYPE_ORGANIZATION_TRUNK)
		->where('delete','<',ModelUtil::getDeleteLevel())
		->select(['id','name'])
		->orderBy('sort','ASC')
		->get();
		if(empty($trunks) === false){
			foreach ($trunks as &$item){

				$tmp['text'] = $item->name;
				$tmp['id'] = TreeUtil::recordidToNodeid($item ->id, TreeUtil::TRUNK);// 数据库记录的 id 转换为 jstree的 id
				$tmp['type'] = 'trunk';
				$tmp['children'] = true;
				$jsonArray[] = $tmp;// 高效的往数组中增加一个元素
			}
		}
		
		$leaves = User::where('company_id','=',$companyId)
		->where('tree_trunk_id','=',$nodeId)
		->where('delete','<',ModelUtil::getDeleteLevel())
		->select(['id','name'])
		->orderBy('sort','ASC')
		->get();
		if(empty($leaves) === false){
			foreach ($leaves as &$item){
				$tmp['text'] = $item->name;
				$tmp['id'] = TreeUtil::recordidToNodeid($item ->id, TreeUtil::LEAF);
				$tmp['type'] = 'user';
				$tmp['children'] = false;
				$jsonArray[] = $tmp;// 高效的往数组中增加一个元素
			}
		}
		return $jsonArray;
	}
	
	/*
	 * 根据公司 id 和 节点 id  获取 数据库中表记录
	 * 返回数据表模型记录
	 */
	public static function getANodeData($companyId,$nodeId){
		if(empty($nodeId)){
			return [];
		}
		$nodeType = TreeUtil::getNodeType($nodeId);
		$nodeId = TreeUtil::nodeidToRecordid($nodeId);
		
		$array = [];
		if( $nodeType == TreeUtil::LEAF){
			$node = Material::where('id','=',$nodeId)->first();
			if(empty($node) === false){
				$array = ['id' =>TreeUtil::recordidToNodeid($node->id, 
						TreeUtil::LEAF) , 'name' => $node->name ,
						'mainType' =>$node->main_type,'type' => $node->type,
						'number' => $node->material_number, 
						'price' =>$node->price,'description'=>$node->description,
						'pictureUrl' =>$node->picture_url
				];	
				}
		}else if( $nodeType == TreeUtil::TRUNK){
			$node = TreeTrunk::where('id','=',$nodeId)->first();
			if(empty($node) === false){
				$array = ['id' =>TreeUtil::recordidToNodeid($node->id,
						TreeUtil::TRUNK), 'name' => $node->name ,
						'type' => $node->type,'number' => $node->number, 
						'description'=>$node->description];
			}
		}
		return $array;
	}
	/*
	 * 根据公司 id 和 节点 id  获取 数据库中表记录
	 * 返回数据表模型记录
	 */
	public static function getANodeDataModel($companyId,$nodeId,$treeType){
		if(empty($nodeId)){
			abort(404,'id不能为空');
		}
		$nodeType = TreeUtil::getNodeType($nodeId);
		$nodeId = TreeUtil::nodeidToRecordid($nodeId);
		if( TreeUtil::LEAF == $nodeType){
			if(TreeUtil::TYPE_MATERIAL_TRUNK == $treeType){
				return Material::findOrFail($nodeId);
			}elseif (TreeUtil::TYPE_ORGANIZATION_TRUNK == $treeType){
				return User::findOrFail($nodeId);
			}else{
				abort(404,'没有这种类型的树叶节点');
			}
		}elseif ( TreeUtil::TRUNK == $nodeType){
			return TreeTrunk::findOrFail($nodeId);
		}
		abort(404,'没有此类型的节点');
	}
	/*获取组织机构树模型的节点信息
	 * 根据公司 id 和 节点 id  获取 数据库中表记录 -- 获取 tree_trunk 表和 user 表
	 * 返回数据表模型记录
	 */
	public static function getAOrganizationNodeData($companyId,$nodeId){
		if(empty($nodeId)){
			return [];
		}
		$nodeType = TreeUtil::getNodeType($nodeId);
		$nodeId = TreeUtil::nodeidToRecordid($nodeId);
		
		$array = [];
		if( $nodeType == TreeUtil::LEAF){
			$node = User::findOrFail($nodeId);
			if(empty($node) === false){
				$array = ['id' =>TreeUtil::recordidToNodeid($node->id, TreeUtil::LEAF) , 
						'name' => $node->name ,'password' => $node->password,
						'email' => $node->email,'number' => $node->number,
						'jobType' => $node->job_type ,
				];	
				}
		}else{
			$node = TreeTrunk::findOrFail($nodeId);
			if(empty($node) === false){
				$array = ['id' =>TreeUtil::recordidToNodeid($node->id,TreeUtil::TRUNK),
						'name' => $node->name ,'type' => $node->type,
						'number' => $node->number, 'description'=>$node->description];
			}
		}
		return $array;
	}
	
}
