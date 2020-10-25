<?php

/**
 * 改写框架Json类方法
 */

namespace yii\helpers;

class Json extends BaseJson
{

	/**
	 * Encodes the given value into a JSON string.
	 * @param type $value
	 * @param type $options
	 * @return type
	 */
	public static function encode($value, $options = 320)
	{
		if (empty($value))
		{
			$options = JSON_FORCE_OBJECT;
		}
		return parent::encode($value, $options);
	}

}
