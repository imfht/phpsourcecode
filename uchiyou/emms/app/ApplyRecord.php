<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Util\PaginateUtil;

class ApplyRecord extends Model
{
	/**
	 * 与模型关联的数据表
	 *
	 * @var string
	 */
	protected $table = 'mm_apply_records';
	// 4 需要审批人的申请表类型
	const APPLYRECORD_TYPE_PURCHASE = 1;
	const APPLYRECORD_TYPE_REPAIRE = 2;
	// 申请记录的状态
	const APPLYRECORD_STATUS_APPLY = 1;
	const APPLYRECORD_STATUS_AGREE = 3;
	const APPLYRECORD_STATUS_REJECT = 4;
	const APPLYRECORD_STATUS_ALLOCATION = 5;
	const APPLYRECORD_STATUS_RECEIVED = 6;
	const APPLYRECORD_STATUS_DELETED = 7;//  deleted 应该是 statues 中最大的值
	/**
	 * 
	 * 获取物资购买申请的记录
	 * @param unknown $companyId  公司表的id
	 * @param unknown $column
	 * @param unknown $method
	 * @param unknown $value
	 * @param unknown $statuses
	 * @param unknown $statusOperator
	 * @param unknown $delete
	 * @return unknown
	 */
	public static function getRecord($companyId,$column,$method,
			$value,$statuses,$statusOperator,$delete){
		
		return DB::table('mm_apply_records')
		->leftJoin('users','users.id','=','mm_apply_records.user_id')
		->leftJoin('mm_tree_trunk as t','t.id','=','users.tree_trunk_id')
		->select('users.name as userName','users.number as employeeNumber',
				'users.phone as phone','t.name as departmentName',
				'mm_apply_records.*')
				->where('mm_apply_records.company_id','=',$companyId)
				->$method($column,$value)
				->where('mm_apply_records.statuses',$statusOperator,$statuses)
				->where('mm_apply_records.delete','<',$delete)
				->orderBy('mm_apply_records.updated_at','desc')
				->paginate(PaginateUtil::PAGE_SIZE);
	}
	/**
	 * 用于导出到excel,没有分页
	 * @param unknown $companyId
	 * @param unknown $column
	 * @param unknown $method
	 * @param unknown $value
	 * @param unknown $statuses
	 * @param unknown $statusOperator
	 * @param unknown $delete
	 * @return unknown
	 */
	public static function getAllRecord($companyId,$column,$method,
			$value,$statuses,$statusOperator,$delete){
		
		return DB::table('mm_apply_records')
		->leftJoin('users','users.id','=','mm_apply_records.user_id')
		->leftJoin('mm_tree_trunk as t','t.id','=','users.tree_trunk_id')
		->select('users.name as userName','users.number as employeeNumber',
				'users.phone as phone','t.name as departmentName',
				'mm_apply_records.*')
				->where('mm_apply_records.company_id','=',$companyId)
				->$method($column,$value)
				->where('mm_apply_records.statuses',$statusOperator,$statuses)
				->where('mm_apply_records.delete','<',$delete)
				->orderBy('mm_apply_records.updated_at','desc')
				->get();
	}
	public static function getSearchRecordsResult($searchColumn,$searchValue,
			$companyId,$column,$method,
			$value,$statuses,$statusOperator,$delete){
		
		return DB::table('mm_apply_records')
		->leftJoin('users','users.id','=','mm_apply_records.user_id')
		->leftJoin('mm_tree_trunk as t','t.id','=','users.tree_trunk_id')
		->select('users.name as userName','users.number as employeeNumber',
				'users.phone as phone','t.name as departmentName',
				'mm_apply_records.*')
				->where('mm_apply_records.company_id','=',$companyId)
				->$method($column,$value)
				->where('mm_apply_records.statuses',$statusOperator,$statuses)
				->where('mm_apply_records.delete','<',$delete)
				->where($searchColumn,'like','%'.$searchValue.'%')
				->orderBy('mm_apply_records.updated_at','desc')
				->paginate(PaginateUtil::SEARCH_PAGE_SIZE);
	}
}
