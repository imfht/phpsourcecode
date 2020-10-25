<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

/**
 * 可通过第三方软件定时执行改方法
 */
class ScheduleRun extends Admin
{
	public function index()
	{
		$res = model('common/Schedule')->runScheduleList();

		echo $res;exit;
	}

}