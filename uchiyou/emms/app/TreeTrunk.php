<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Util\ModelUtil;

class TreeTrunk extends Model
{
	// 类型常量
	const TYPE_DEPARTMENT = 1;
	const TYPE_MATERIAL_DEVIDE = 2;
	/**
	 * 与模型关联的数据表
	 *
	 * @var string
	 */
	protected $table = 'mm_tree_trunk';
	/*
	 * 获取一个部门的物资总数. 需要递归的查询一个树枝
	 * @param $trunkId 树分支的 id
	 * @return 该部门的物资总数量和物资的中价值的数组，['sum'=>?,'totalValue'=>?];
	 * 否则返回 null
	 */
	public static function getSumOfATrunk($trunkId){
		
		$result = ['materialCounts'=>0,'totalPrice'=>0,
				'userCounts'=>0,'repaireCounts'=>0];
		// 1 物资信息统计
		$materialsResult = DB::table('mm_material')
		->select(DB::raw('count(*) as counts,sum(price) as totalPrice'))
		->where('company_id', $trunkId)
		->where('tree_trunk_id', $trunkId)
		->where('delete','<',ModelUtil::getDeleteLevel())
		->first();
		if(empty($materialsResult) === false){
			$result['materialCounts'] += $materialsResult->counts;
			$result['totalPrice'] += $materialsResult->totalPrice;
		}
		// 2 员工数量统计
		$userResult = DB::table('users')
		->select(DB::raw('count("id") as counts'))
		->where('tree_trunk_id',$trunkId)
		->where('delete','<',ModelUtil::getDeleteLevel())
		->first();
		if(empty($userResult) === false){
			$result['userCounts'] +=$userResult->counts;
		}
		// 3 维修物资数量统计
		$repaireResult = DB::table('mm_repaire_record as r')
		->leftJoin('mm_material as m','r.material_id','=','m.id')
		->select(DB::raw('count("r.id") as counts'))
		->where('r.delete','<',ModelUtil::getDeleteLevel())
		->where('m.delete','<',ModelUtil::getDeleteLevel())
		->where('m.tree_trunk_id',$trunkId)
		->first();
		if(empty($repaireResult)===false){
			$result['repaireCounts'] +=$repaireResult->counts;
		}
		
		$treeTrunks = TreeTrunk::where('parent_id',$trunkId)
		->where('delete','<',ModelUtil::getDeleteLevel())// 正常状态的记录
		->get();
		foreach ($treeTrunks as $treeTrunk){
			$subResult = TreeTrunk::getSumOfATrunk($treeTrunk->id);
			$result['materialCounts'] += $subResult['materialCounts'];
			$result['totalPrice'] += $subResult['totalPrice'];
			$result['userCounts'] += $subResult['userCounts'];
			$result['repaireCounts'] += $subResult['repaireCounts'];
		}
		return $result;
	}
	
}
