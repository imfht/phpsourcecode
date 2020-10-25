<?php

namespace app\pay\job;

use \think\Db;
use \think\queue\Job;

class Befen
{

	public function fire(Job $job, $data = [])
	{
		
	}

	public function test(Job $job, $data = [])
	{

		//重试次数
		$job->attempts();
		//删除任务
		$job->delete();
		//重新发布任务
		$job->release();

	}

}

