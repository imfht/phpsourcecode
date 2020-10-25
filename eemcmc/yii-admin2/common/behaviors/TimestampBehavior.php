<?php

namespace common\behaviors;

/**
 * 时间处理行为助手
 * @author ken <vb2005xu@qq.com>
 */
class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{

	/**
	 * @inheritdoc
	 */
	protected function getValue($event)
	{
		if ($this->value instanceof Expression)
		{
			return $this->value;
		}
		else
		{
			return $this->value !== null ? call_user_func($this->value, $event) : date('Y-m-d H:i:s');
		}
	}

}
