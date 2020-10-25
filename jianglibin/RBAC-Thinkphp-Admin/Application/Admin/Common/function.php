<?php

/**
 * 将数组组装成树状结构,要求数据必须是二位数组,数组里必须包含pid字段
 * @param  [type]  $arr  [目标数组]
 * @param  [type]  $arr  [目标数组的主键字段]
 * @param  integer $pid  [数组pid的起始点]
 * @return [type]        [description]
 */
function array_tree($arr, $colum='',$pid = 0 )
{	
	if ( empty($arr) || !isset($arr[0]['pid']) ) 
	{
		return [];
	}

	$array_tree = [];

	foreach ($arr as $v) 
	{
		if ( $v['pid'] == $pid )
		{
			$v['son'] = array_tree($arr, $colum,$v[$colum]);
			$array_tree[] = $v;
		}
	}

	return $array_tree;

}