<?php

namespace console\models;

use Yii;

/**
 * This is the model class for table "{{%console_autotasks}}".
 *
 * 基本属性
 * @property integer $id 任务id
 * @property string $name 任务名称
 * @property string $time 需要执行时间
 * @property integer $last_time 上次执行时间
 * @property string $fnc 执行函数
 * @property integer $status 任务状态
 * @property string $created_at 创建时间
 */
class Task extends \common\models\BaseModel
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%console_tasks}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name', 'time', 'fnc'], 'required'],
			[['last_time', 'status'], 'integer'],
			[['created_at'], 'safe'],
			[['name'], 'string', 'max' => 100],
			[['time'], 'string', 'max' => 50],
			[['fnc'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => '任务id',
			'name' => '任务名称',
			'time' => '执行时间设定',
			'last_time' => '最后执行时间',
			'fnc' => '需要执行的函数',
			'status' => '任务状态, 1正常, 2关闭',
			'created_at' => '创建时间',
		];
	}

	/**
	 * 保存前执行
	 * @param array $insert
	 */
	public function beforeSave($insert)
	{
		if ($this->isNewRecord)
		{
			$this->created_at = date('Y-m-d H:i:s');
		}
		return true;
	}

	/**
	 * 是否执行任务
	 * @return boolean
	 */
	public function isRun()
	{
		$now_time = time();
		$times = getdate();
		list($m, $d, $h, $i, $s) = explode(' ', $this->time);
		$interval = 0;

		//月份判断
		if ($m == '*' || intval($m) == $times['mon'])
		{
			$is_m = true;
		}
		else
		{
			$is_m = false;
		}

		//日判断
		$d = explode('/', $d);
		if ($d[0] == '*' || intval($d[0]) == $times['mday'])
		{
			$is_d = true;
			if (count($d) == 2)
			{
				$interval += (intval($d[1]) * 60 * 60 * 24);
			}
		}
		else
		{
			$is_d = false;
		}

		//时判断
		$h = explode('/', $h);
		if ($h[0] == '*' || intval($h[0]) == $times['hours'])
		{
			$is_h = true;
			if (count($h) == 2)
			{
				$interval += (intval($h[1]) * 60 * 60);
			}
		}
		else
		{
			$is_h = false;
		}

		//分判断
		$i = explode('/', $i);
		if ($i[0] == '*' || intval($i[0]) == $times['minutes'])
		{
			$is_i = true;
			if (count($i) == 2)
			{
				$interval += (intval($i[1]) * 60);
			}
		}
		else
		{
			$is_i = false;
		}

		//秒判断
		$s = explode('/', $s);
		if ($s[0] == '*' || intval($s[0]) == $times['seconds'])
		{
			$is_s = true;
			if (count($s) == 2)
			{
				$interval += (intval($s[1]));
			}
		}
		else
		{
			$is_s = false;
		}

		//计算间隔时间是否执行
		$last_time = !empty($this->last_time) ? $this->last_time : 0;
		if ($now_time - $last_time >= $interval)
		{
			$is_run = true;
		}
		else
		{
			$is_run = false;
		}

		if ($is_m && $is_d && $is_h && $is_i && $is_s && $is_run)
		{
			$this->last_time = time();
			$this->save();
			return true;
		}
		else
		{
			return false;
		}
	}

}
