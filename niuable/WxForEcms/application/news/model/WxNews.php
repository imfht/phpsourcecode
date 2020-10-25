<?php
namespace app\news\model;

use think\Model;

class WxNews extends Model {
	protected $type = [ 
			'send_time' => 'timestamp:Y-m-d H:i',
			'update_time' => 'timestamp',
			'create_time' => 'timestamp' 
	];
}