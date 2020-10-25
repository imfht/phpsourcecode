<?php
namespace app\msg\model;
use think\Model;
class WxMsg extends Model{
	protected $type = [
			'update_time'  =>  'timestamp',
			'create_time'  =>  'timestamp',
	];
}