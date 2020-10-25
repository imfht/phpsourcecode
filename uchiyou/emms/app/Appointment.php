<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Util\PaginateUtil;

class Appointment extends Model
{
	/**
	 * 与模型关联的数据表
	 *
	 * @var string
	 */
	protected $table = 'mm_appointments';
	/**
	 * 该模型是否被自动维护时间戳
	 *
	 * @var bool
	 */
	public $timestamps = false;
	// 2 预约表状态常量
	const APPOINTMENT_STATUS_APPOINTED = 1;
	const APPOINTMENT_STATUS_CANCELED = 2;
	// delete 字段
	public static function getRecord($companyId,$column,$method,
			$value,$status,$statusOperator,$delete){
		return DB::table('mm_appointments')
		->leftJoin('mm_material','mm_material.id','=','mm_appointments.material_id')
		->leftJoin('users as u','u.id','=','mm_appointments.user_id')
		->select('mm_appointments.id as recordId','mm_appointments.start_time',
				'mm_appointments.status as appointStatus',
				'u.name as userName','u.phone','mm_material.*')
				->where('mm_appointments.company_id','=',$companyId)
				->$method($column,$value)
				->where('mm_appointments.status',$statusOperator,$status)
				->where('mm_appointments.delete','<',$delete)
				->orderBy('mm_appointments.finish_time','desc')
				->paginate(PaginateUtil::PAGE_SIZE);
	}
	
	/**
	 * 获取预约记录的搜索结果
	 * @param unknown $searchColumn
	 * @param unknown $searchValue
	 * @param unknown $companyId
	 * @param unknown $column
	 * @param unknown $method
	 * @param unknown $value
	 * @param unknown $status
	 * @param unknown $statusOperator
	 * @param unknown $delete
	 * @return unknown	# 返回满足搜索条件的记录
	 */
	public static function getSearchRecordsResult($searchColumn,$searchValue,
			$companyId,$column,$method,
			$value,$status,$statusOperator,$delete){
		return DB::table('mm_appointments')
		->leftJoin('mm_material','mm_material.id','=','mm_appointments.material_id')
		->leftJoin('users','users.id','=','mm_appointments.user_id')
		->select('mm_appointments.id as recordId','mm_appointments.start_time',
				'mm_appointments.status as appointStatus',
				'users.name as userName','users.phone','mm_material.*')
				->where('mm_appointments.company_id','=',$companyId)
				->$method($column,$value)
				->where('mm_appointments.status',$statusOperator,$status)
				->where('mm_appointments.delete','<',$delete)
				->where($searchColumn,'like','%'.$searchValue.'%')
				->orderBy('mm_appointments.finish_time','desc')
				->paginate(PaginateUtil::SEARCH_PAGE_SIZE);
	}
}
