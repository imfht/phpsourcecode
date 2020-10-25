<?php

namespace app\common\model;

use \app\common\Model;

use \think\Db;
use \think\Cache;
use \think\Cookie;
use \think\Session;

class AgentLevel extends Model
{

	//protected $pk = '_id_';

	protected $resultSetType = 'collection';

	protected function initialize()
	{
		parent::initialize();
	}

	public static function getLevel()
	{
		$list = self::all()->toArray();
		$level = [];
		foreach($list as $key => $val) {
			$level[$val['level_id']] = $val;
		}
		return $level;
	}

}

