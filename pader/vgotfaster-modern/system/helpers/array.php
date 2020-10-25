<?php
/**
 * VgotFaster PHP Framework
 *
 * PHP Array Helpers
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2012, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

/**
 * Array Deep Merge
 *
 * Support many dimensions array merge
 *
 * @param array $var,$var2,$var3....
 * @return array
 */
if(!function_exists('arrayDeepMerge'))
{
	function arrayDeepMerge() {
		$args = func_get_args();
		if (!is_array($args)) return NULL;

		$target = array();

		foreach ($args as $cur) {
			if (is_array($cur)) {
				if (count($cur) == count($cur, COUNT_RECURSIVE)) {
					$target = $target + $cur;
				} else {
					$targetKeys = array_keys($target);
					$curKeys = array_keys($cur);

					foreach (array_intersect($targetKeys,$curKeys) as $key) {  //同键名对比
						if (is_array($target[$key]) && is_array($cur[$key])) {
							if (count($target[$key]) == count($target[$key], COUNT_RECURSIVE) && count($cur[$key]) == count($cur[$key], COUNT_RECURSIVE)) {
								$target[$key] = $target[$key] + $cur[$key];
							} else {
								$target[$key] = arrayDeepMerge($target[$key],$cur[$key]);
							}
						} else {
							$target[$key] = $cur[$key];
						}
					}

					foreach (array_diff($curKeys,$targetKeys) as $key) {  //异键名(基不存在)便直接添加
						$target[$key] = $cur[$key];
					}
				}
			} else continue;
		}

		return $target;
	}
}

/**
 * array_column()
 *
 * 从数组中取一列作为值返回
 * 用法参考 PHP 5.4 新增的内置同名函数
 *
 * @example http://www.php.net/manual/zh/function.array-column.php
 *
 * @param array $input
 * @param string $columnKey
 * @param string $indexKey
 * @return array
 */
if (!function_exists('array_column'))
{
    function array_column($input, $columnKey, $indexKey=null) {
        $result = array();
        foreach ($input as $key => $row){
            $val = isset($row[$columnKey]) ? $row[$columnKey] : null;

            if (!is_null($indexKey)) {
                $key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
            }

            $result[$key] = $val;
        }

        return $result;
    }
}
