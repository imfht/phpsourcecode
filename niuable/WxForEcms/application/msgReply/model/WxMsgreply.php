<?php
namespace app\msgReply\model;
use think\Model;
class WxMsgreply extends Model{
	protected $type = [
			'update_time'  =>  'timestamp',
			'create_time'  =>  'timestamp',
	];
}