<?php

namespace console\controllers;

use console\models\Task;
use common\helpers\String;

/**
 * 计划任务控制器
 * @author ken <vb2005xu@qq.com>
 */
class TaskController extends BaseController
{

	/**
	 * 执行任务
	 * @param int $id 任务id
	 * @return int
	 */
	public function actionRun($id = 0)
	{
		if (!is_numeric($id))
		{
			return $this->failure("Invalid id!");
		}
		$task = Task::findOne($id);
		if (empty($task))
		{
			return $this->failure("Task does not exist!");
		}
		if (!$task->isRun())
		{
			return $this->failure("Did not execute!");
		}
		//执行任务
		$this->run($task->fnc);
		return $this->success("Execute successfully!");
	}

	/**
	 * 执行所有任务，该方法用于linux crontab定时执行
	 */
	public function actionRuns()
	{
		set_time_limit(0);
		$tasks = Task::findAll(['status' => 1]);
		foreach ($tasks as $key => $task)
		{
			if (!$task->isRun())
			{
				continue;
			}
			//执行任务
			$this->run($task->fnc);
		}
		$time = date('Y-m-d H:i:s');
		//return $this->success("{$time}# execute successfully!");
	}

	/**
	 * 显示当前所有任务
	 */
	public function actionIndex()
	{
		$tasks = Task::find()->asArray()->all();
		$string = " id	任务名称		执行时间	  执行函数		\n";
		foreach ($tasks as $key => $task)
		{
			$string .= " {$task['id']}	{$task['name']}	{$task['time']}	 {$task['fnc']}\n";
		}

		return $this->success($string);
	}

	/**
	 * 创建新任务
	 * @param string $name 任务名称
	 * @param string $time 执行时间
	 * @param string $fnc 执行函数
	 */
	public function actionCreate($name, $time, $fnc)
	{
		$attributes = [
			'name' => $name,
			'time' => $time,
			'fnc' => $fnc,
		];
		$task = Task::create($attributes);
		if ($task->hasErrors())
		{
			echo $task->getError();
			return 1;
		}
		return $this->success('Create successfully!');
	}

	/**
	 * 删除任务
	 * @param int $id 任务id
	 * @return int
	 */
	public function actionDelete($id)
	{
		if (!is_numeric($id))
		{
			return $this->failure("Invalid id!");
		}
		$task = Task::findOne($id);
		if (empty($task))
		{
			return $this->failure("Task does not exist!");
		}
		$task->delete();
		return $this->success("Delete successfully!");
	}

}
