<?php
namespace app\index\model;
use think\Model;
class WxWx extends Model{
	protected $type = [
			'update_time'  =>  'timestamp',
			'create_time'  =>  'timestamp',
	];
}