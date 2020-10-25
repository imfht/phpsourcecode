<?php
/*
 * @varsion		EasyWork系统 1.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, 95era, Inc.
 * @link		http://www.d-winner.com
 */
 
//二维数组排序函数
/*
$arr		输入的二位数组
$keys		需要排序的字段
$mode		是否保持原来的键名，keep为保持、nokeep为重新建立
$type		排序方式 asc为升序。desc为降序
*/
function array_sort($arr,$keys,$mode='nokeep',$type='asc'){ 
	$keysvalue = $new_array = array();
	foreach ($arr as $k=>$v){
		$keysvalue[$k] = iconv('UTF-8', 'GB2312',$v[$keys]);
	}
	if($type == 'asc'){
		asort($keysvalue);
	}else{
		arsort($keysvalue);
	}
	reset($keysvalue);
	if($mode=='keep'){
		foreach ($keysvalue as $k=>$v){
			$new_array[$k] = $arr[$k];
		}
	}else{
		foreach ($keysvalue as $k=>$v){
			$new_array[] = $arr[$k];
		}
	}
	
	return $new_array; 
}