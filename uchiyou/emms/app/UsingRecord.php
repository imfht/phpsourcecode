<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Util\PaginateUtil;

class UsingRecord extends Model
{
	/**
	 * 与模型关联的数据表
	 *
	 * @var string
	 */
	protected $table = 'mm_using_record';
	/**
	 * 该模型是否被自动维护时间戳
	 *
	 * @var bool
	 */
	public $timestamps = false;
	/*
	 * 该模型的  has_deliver 字段常量
	 */
	const HAS_DELIVER_NO = 0;
	const HAS_DELIVER_YES = 1;
	const HAS_DELIVER_ACCEPTED = 2;
	const HAS_DELIVER_DELIVERED = 3;
	const HAS_DELIVER_RETURNED = 4;
	/**
	 * @param $column   # UsingRecord 的字段名称
	 * @param $columnOperator  #　UsingRecord 的 $column 字段名称条件查询的运算符号
	 * @param $value  ＃　UsingRecord 的$column 字段名称 条件查询的值
	 * @param $time ＃　UingRecord　的　deadline 字段条件查询对应的值
	 * @param $timeOperator #　UsingRecord 的 deadlne 字段名称条件查询的运算符号
	 * @param $delete ＃　UingRecord　的　delete 字段条件查询对应的值
	 */
	public static function getRecords($companyId,$column,$method,$value,$time,$timeOperator,$delete){
		
		return DB::table('mm_using_record')
		->leftJoin('mm_material','mm_material.id','=','mm_using_record.material_id')
		->leftJoin('users','users.id','=','mm_using_record.user_id')
		->select('mm_material.name as materialName',
				'mm_material.material_number as materialNumber',
				'mm_material.description as materialDescription',
				'users.name as userName','users.phone as phone',
				'mm_material.picture_url as pictureUrl',
				'mm_using_record.*')
				->where('mm_using_record.deadline',$timeOperator,$time)
				->where('mm_using_record.company_id','=',$companyId)
				->$method($column,$value)
			//	->whereIn($column,$value)
				->where('mm_using_record.delete','<',$delete)
				->orderBy('mm_using_record.startTime','desc')
				->paginate(PaginateUtil::PAGE_SIZE);
	}
	/**
	 * 参考 getRecords, 需要从对应的历史记录中搜索结果
	 */
	public static function getSearchRecordsResult($searchColumn,$searchValue,
			$companyId,$column,$method,$value,
			$time,$timeOperator,$delete){
		
		return DB::table('mm_using_record')
		->leftJoin('mm_material','mm_material.id','=','mm_using_record.material_id')
		->leftJoin('users','users.id','=','mm_using_record.user_id')
		->select('mm_material.name as materialName',
				'mm_material.material_number as materialNumber',
				'mm_material.description as materialDescription',
				'users.name as userName','users.phone as phone',
				'mm_material.picture_url as pictureUrl',
				'mm_using_record.*')
				->where('mm_using_record.company_id','=',$companyId)
				->$method($column,$value)
				->where('mm_using_record.deadline',$timeOperator,$time)
				->where('mm_using_record.delete','<',$delete)
				->where($searchColumn,'like','%'.$searchValue.'%')
				->orderBy('mm_using_record.startTime','desc')
				->paginate(PaginateUtil::SEARCH_PAGE_SIZE);// 对搜索的结果分页目前没做好
	}
	
	
}
