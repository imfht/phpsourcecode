<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Util\PaginateUtil;

class RepaireRecord extends Model
{
	/**
	 * 与模型关联的数据表
	 *
	 * @var string
	 */
	protected $table = 'mm_repaire_record';
	// 5 维修表的状态
	const REPAIRERECORD_STATUS_APPLY = 1;
	const REPAIRERECORD_STATUS_REPAIRE = 2;
	const REPAIRERECORD_STATUS_SUCESS = 3;
	const REPAIRERECORD_STATUS_FAIL = 4;
	const REPAIRERECORD_STATUS_SHUTDOWN = 5;
	public static function getRecord($companyId,$status,$operator,$delete,$column,$method,$value){
		return DB::table('mm_repaire_record')
		->leftJoin('mm_material','mm_material.id','=','mm_repaire_record.material_id')
		->leftJoin('users','users.id','=','mm_repaire_record.user_id')
		->leftJoin('mm_tree_trunk','mm_repaire_record.id','=','users.tree_trunk_id')
		->select('mm_repaire_record.id as recordId',
				'mm_repaire_record.fault_description as faultDescription',
				'mm_repaire_record.created_at as upTime','users.name as userName',
				'users.phone as phone','mm_tree_trunk.name as departmentName',
				'mm_repaire_record.status as repaireStatus','mm_material.*')
				->where('mm_repaire_record.status',$operator,$status)
				->where('mm_repaire_record.delete','<',$delete)
				->where('mm_repaire_record.company_id','=',$companyId)
				->$method($column,$value)
				->orderBy('mm_repaire_record.updated_at','desc')
				//->where('mm_material.status',Material::MATERIAL_STATUS_PROBLEM)
		->paginate(PaginateUtil::PAGE_SIZE);
	}
	public static function getSearchRecordsResult($searchColumn,$searchValue,
			$companyId,$status,$operator,$delete,
			$column,$method,$value){
		
		return DB::table('mm_repaire_record')
		->leftJoin('mm_material','mm_material.id','=','mm_repaire_record.material_id')
		->leftJoin('users','users.id','=','mm_repaire_record.user_id')
		->leftJoin('mm_tree_trunk','mm_repaire_record.id','=','users.tree_trunk_id')
		->select('mm_repaire_record.id as recordId',
				'mm_repaire_record.fault_description as faultDescription',
				'mm_repaire_record.created_at as upTime','users.name as userName',
				'users.phone as phone','mm_tree_trunk.name as departmentName',
				'mm_repaire_record.status as repaireStatus','mm_material.*')
				->where('mm_repaire_record.status',$operator,$status)
				->where('mm_repaire_record.company_id','=',$companyId)
				->$method($column,$value)
				->where($searchColumn,'like','%'.$searchValue.'%')
				->where('mm_repaire_record.delete','<',$delete)
				->orderBy('mm_repaire_record.updated_at','desc')
				//->where('mm_material.status',Material::MATERIAL_STATUS_PROBLEM)
		->paginate(PaginateUtil::SEARCH_PAGE_SIZE);
	}
}
