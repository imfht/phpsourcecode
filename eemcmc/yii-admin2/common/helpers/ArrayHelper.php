<?php

namespace common\helpers;

/**
 * 扩展框架数组辅助类库
 *
 * @author ken <vb2005xu@qq.com>
 */
class ArrayHelper extends \yii\helpers\BaseArrayHelper
{

	/**
	 * 获取指定的key值
	 * @param array $array 需要操作的数据
	 * @param array $keys keys
	 * @return array 结果
	 */
	static function onlyValue($array, $keys)
	{
		return array_intersect_key($array, array_flip((array) $keys));
	}

	static function normalize($input, $delimiter = ',')
	{
		if (!is_array($input))
		{
			$input = explode($delimiter, $input);
		}
		$input = array_map('trim', $input);
		return array_filter($input, 'strlen');
	}

	/**
	 * 随机获取数组的n条元素
	 * @param array $array
	 * @param int $num
	 * @return array
	 */
	static function getRands($array, $num)
	{
		$keys = array_rand($array, $num);
		$data = [];
		if (is_array($keys))
		{
			foreach ($keys as $key)
			{
				$data[] = $array[$key];
			}
		}
		else
		{
			$data = $array[$keys];
		}
		return $data;
	}

}

?>
