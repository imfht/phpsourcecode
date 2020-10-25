<?php
namespace app\menu\model;
use think\Model;
class WxMenu extends Model{
	protected $type = [
			'update_time'  =>  'timestamp',
			'create_time'  =>  'timestamp',
			'menu'=>'array',
	];
}