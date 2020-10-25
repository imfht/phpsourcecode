<?php
namespace app\user\model;

use think\Model;

class WxUser extends Model {
	protected $type = [ 
			'update_time' => 'timestamp',
			'create_time' => 'timestamp',
			'subscribe_time' => 'timestamp' 
	];
}