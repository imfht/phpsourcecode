<?php
/**
 * 用于判断是否为今天还是昨天
 * @param string $publishDate
 */
function smarty_modifier_wtime($publishDate) {
	
	if (empty ( $publishDate )) {
		return "未知";
	}
	
	$dur = time () - $publishDate;
	$current = date ( "Y", time () );
	$publish = date ( "Y", $publishDate );
	//判断昨天 最小
	$yesterday = strtotime ( "-1 day",strtotime(date ( "Ymd", time ()) ) );
	
	$today = strtotime ( date ( "Ymd", time () ) );
	
	if ($publishDate > $yesterday && $publishDate < $today) {
		return "昨天 " . date ( "H:i", $publishDate );
	}
	
	if ($publishDate<$yesterday){
	   return date ( "n月j日", $publishDate );
	}
	
	switch ($dur) {
		case $dur < 60 :
			return "刚刚";
			break;
		case $dur < 3600 && $dur > 60 :
			return "" . floor ( $dur / 60 ) . '分钟前';
			break;
		
		case $dur < 86400 && $dur > 3600 :
			return "" . floor ( $dur / 3600 ) . "小时前";
			break;
	}
	//是否是今年的
	if ($current != $publish) {
		return date ( "Y年n月j日", $publishDate );
	}
	
	return "无法获取时间";

}

