<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
	/**
	 * 与模型关联的数据表
	 *
	 * @var string
	 */
	protected $table = 'mm_material';
	// 1  物资表状态常量
	const MATERIAL_STATUS_AVAILABLE = 1;// 可用
	const MATERIAL_STATUS_WASRENT = 2;// 被租用中
	const MATERIAL_STATUS_PROBLEM = 3;// 故障中
	const MATERIAL_STATUS_DISCARD = 4;// 报废状态（报废状态值应该大于前三种状态）
	const MATERIAL_STATUS_WASAPPOINTMENT_SELF = 15;// 当前自己在预约，（此状态仅用于前端页面显示，不保存于数据库中）
	// 2  main_type 字段常量
	const MATERIAL_MAIN_TYPE_FIXED_ASSET = 1;
	const MATERIAL_MAIN_TYPE_NOT_FIXED_ASSET = 2;
	
	
	
}
