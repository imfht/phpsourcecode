<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2020 http://zswin.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: zswin.cn
// +----------------------------------------------------------------------

function toDate($time, $format = 'Y-m-d H:i:s') {
	if (empty($time)) {
		return '';
	}
	$format = str_replace('#', ':', $format);
	return date($format, $time);
}
/**
 * 友好的时间显示
 *
 * @param int    $sTime 待显示的时间
 * @param string $type  类型. normal | mohu | full | ymd | other
 * @param string $alt   已失效
 * @return string
 */
function friendlyDate($sTime,$type = 'normal',$alt = 'false') {
    if (!$sTime)
        return '';
    //sTime=源时间，cTime=当前时间，dTime=时间差
    $cTime      =   time();
    $dTime      =   $cTime - $sTime;
    $dDay       =   intval(date("z",$cTime)) - intval(date("z",$sTime));
    //$dDay     =   intval($dTime/3600/24);
    $dYear      =   intval(date("Y",$cTime)) - intval(date("Y",$sTime));
    //normal：n秒前，n分钟前，n小时前，日期
    if($type=='normal'){
        if( $dTime < 60 ){
            if($dTime < 10){
                return '刚刚';    //by yangjs
            }else{
                return intval(floor($dTime / 10) * 10)."秒前";
            }
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
            //今天的数据.年份相同.日期相同.
        }elseif( $dYear==0 && $dDay == 0  ){
            //return intval($dTime/3600)."小时前";
            return '今天'.date('H:i',$sTime);
        }elseif($dYear==0){
            return date("m月d日 H:i",$sTime);
        }else{
            return date("Y-m-d H:i",$sTime);
        }
    }elseif($type=='mohu'){
        if( $dTime < 60 ){
            return $dTime."秒前";
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
        }elseif( $dTime >= 3600 && $dDay == 0  ){
            return intval($dTime/3600)."小时前";
        }elseif( $dDay > 0 && $dDay<=7 ){
            return intval($dDay)."天前";
        }elseif( $dDay > 7 &&  $dDay <= 30 ){
            return intval($dDay/7) . '周前';
        }elseif( $dDay > 30 ){
            return intval($dDay/30) . '个月前';
        }
        //full: Y-m-d , H:i:s
    }elseif($type=='full'){
        return date("Y-m-d , H:i:s",$sTime);
    }elseif($type=='ymd'){
        return date("Y-m-d",$sTime);
    }else{
        if( $dTime < 60 ){
            return $dTime."秒前";
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
        }elseif( $dTime >= 3600 && $dDay == 0  ){
            return intval($dTime/3600)."小时前";
        }elseif($dYear==0){
            return date("Y-m-d H:i:s",$sTime);
        }else{
            return date("Y-m-d H:i:s",$sTime);
        }
    }
}
/**
 * 获取指定月份的第一天开始和最后一天结束的时间戳
 *
 * @param int $y 年份 $m 月份
 * @return array(本月开始时间，本月结束时间)
 */
function datetimeFristAndLast() {
	$t = time();
	$t1 = mktime(0, 0, 0, date("m", $t), date("d", $t), date("Y", $t));
	$t2 = mktime(0, 0, 0, date("m", $t), 1, date("Y", $t));
	$t3 = mktime(0, 0, 0, date("m", $t) - 1, 1, date("Y", $t));
	$t4 = mktime(0, 0, 0, 1, 1, date("Y", $t));
	$e1 = mktime(23, 59, 59, date("m", $t), date("d", $t), date("Y", $t));
	$e2 = mktime(23, 59, 59, date("m", $t), date("t"), date("Y", $t));
	$e3 = mktime(23, 59, 59, date("m", $t) - 1, date("t", $t3), date("Y", $t));
	$e4 = mktime(23, 59, 59, 12, 31, date("Y", $t));
	
	$returnTime = array();
	$returnTime['now'] = $t;
	$returnTime['todaybegintime'] = $t1;
	$returnTime['thismonthbegintime'] = $t2;
	$returnTime['lastmonthbegintime'] = $t3;
	$returnTime['thisyearbegintime'] = $t4;
	$returnTime['todayendtime'] = $e1;
	$returnTime['thismonthendtime'] = $e2;
	$returnTime['lastmonthendtime'] = $e3;
	$returnTime['thisyearendtime'] = $e4;
	return $returnTime;
}
/*
 * $time表示时间戳
 * $data为1表示天数，2表示小时，3表示分钟，4表示秒数
 * */
function timetonow($time,$data=1){
	
	$now=time();
	$t=$now-$time;
	switch ($data){
		
		case 1:
			
			$n=$t/(24*3600);
			
			break;
		case 2:
			$n=$t/(3600);
			break;
		case 3:
			$n=$t/(60);
			break;
		case 4:
			$n=$t;
			break;
		
		
		
		
		
	}
	
	return round($n);
	
}
/*
 * 比较时间段一与时间段二是否有交集
 */

function isMixTime($begintime1, $endtime1, $begintime2, $endtime2) {
	$status = $begintime2 - $begintime1;
	if ($status > 0) {
		$status2 = $begintime2 - $endtime1;
		if ($status2 > 0) {
			return false;
		} else {
			return true;
		}
	} else {
		$status2 = $begintime1 - $endtime2;
		if ($status2 > 0) {
			return false;
		} else {
			return true;
		}
	}
	return false;
}