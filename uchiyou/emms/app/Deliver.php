<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Util\PaginateUtil;

class Deliver extends Model
{
	/**
	 * 与模型关联的数据表
	 *
	 * @var string
	 */
	protected $table = 'mm_delivers';
	/**
	 * 该模型是否被自动维护时间戳
	 *
	 * @var bool
	 */
	public $timestamps = false;
	/*
	 * status 字段常量
	 */
	const STATUS_ORDER = 1;
	const STATUS_DELIVERING = 2;
	const STATUS_ACCEPTED = 3;
	const STATUS_CANCEL = 4;
	const STATUS_LODGE_COMPLAINT = 5;
	/**
	 * mm_using_record 不要轻易的将表名称简写，注意和 $column  对应
	 *
	 * 用户所在部门id 为0，且为管理员，表示超级管理员。
	 * 有 jstree 决定，所以不能用字段常量或配置文件来表示
	 */
	public static function getRecord($companyId,$status,$statusOperator,$column,$method,$value,$delete){
		return DB::table('mm_delivers')
		->leftJoin('mm_using_record','mm_using_record.id','=','mm_delivers.using_record_id')
		->leftJoin('mm_material','mm_material.id','=','mm_using_record.material_id')
		->leftJoin('mm_tree_trunk','mm_tree_trunk.id','=','mm_using_record.tree_trunk_id')
		->select('mm_material.id as materialId',
				'mm_material.name as materialName',
				'mm_material.material_number as materialNumber',
				'mm_material.picture_url as pictureUrl',
				'mm_using_record.startTime as startTime',
				'mm_tree_trunk.name as departmentName','mm_delivers.*')
				->where('mm_delivers.status',$statusOperator,$status)
				->where('mm_material.company_id','=',$companyId)
				->$method($column,$value)
				->where('mm_delivers.delete','<',$delete)
				//->orWhere('delivers.status',Deliver::STATUS_DELIVERING)
		->orderBy('mm_using_record.startTime','desc')
		->paginate(PaginateUtil::PAGE_SIZE);
	}
	public static function getSearchRecordsResult($searchColumn,$searchValue,
			$companyId,$status,$statusOperator,$column,
			$method,$value,$delete){
				return DB::table('mm_delivers')
				->leftJoin('mm_using_record','mm_using_record.id','=','mm_delivers.using_record_id')
				->leftJoin('mm_material','mm_material.id','=','mm_using_record.material_id')
				->leftJoin('mm_tree_trunk','mm_tree_trunk.id','=','mm_using_record.tree_trunk_id')
				->select('mm_material.name as materialName',
						'mm_material.material_number as materialNumber',
						'mm_material.picture_url as pictureUrl',
						'mm_using_record.startTime as startTime',
						'mm_tree_trunk.name as departmentName','mm_delivers.*')
						->where('mm_delivers.status',$statusOperator,$status)
						->where('mm_material.company_id','=',$companyId)
						->$method($column,$value)
						->where('mm_delivers.delete','<',$delete)
				->where($searchColumn,'like','%'.$searchValue.'%')
				//->orWhere('delivers.status',Deliver::STATUS_DELIVERING)
		->orderBy('mm_using_record.startTime','desc')
		->paginate(PaginateUtil::PAGE_SIZE);
	}
}
