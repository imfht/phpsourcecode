<?php
/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author dameng <59262424@qq.com>
 */
if (!function_exists('time_format')) {
    function time_format($time = NULL, $format = 'Y-m-d H:i')
    {
        $time = $time === NULL ? time() : intval($time);
        return date($format, $time);
    }
}
/**
 * 友好的时间显示
 *
 * @param int    $sTime 待显示的时间
 * @param string $type  类型. normal | mohu | full | ymd | other
 * @param string $alt   已失效
 * @return string
 */
if (!function_exists('friendlyDate')) {
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
                    return lang('_JUST_');    //by yangjs
                }else{
                    return intval(floor($dTime / 10) * 10).lang('_SECONDS_AGO_');
                }
            }elseif( $dTime < 3600 ){
                return intval($dTime/60).lang('_MINUTES_AGO_');
                //今天的数据.年份相同.日期相同.
            }elseif( $dYear==0 && $dDay == 0  ){
                //return intval($dTime/3600).lang('_HOURS_AGO_');
                return lang('_TODAY_').date('H:i',$sTime);
            }elseif($dYear==0){
                return date("m月d日 H:i",$sTime);
            }else{
                return date("Y-m-d H:i",$sTime);
            }
        }elseif($type=='mohu'){
            if( $dTime < 60 ){
                return $dTime.lang('_SECONDS_AGO_');
            }elseif( $dTime < 3600 ){
                return intval($dTime/60).lang('_MINUTES_AGO_');
            }elseif( $dTime >= 3600 && $dDay == 0  ){
                return intval($dTime/3600).lang('_HOURS_AGO_');
            }elseif( $dDay > 0 && $dDay<=7 ){
                return intval($dDay).lang('_DAYS_AGO_');
            }elseif( $dDay > 7 &&  $dDay <= 30 ){
                return intval($dDay/7) . lang('_WEEK_AGO_');
            }elseif( $dDay > 30 ){
                return intval($dDay/30) . lang('_A_MONTH_AGO_');
            }
            //full: Y-m-d , H:i:s
        }elseif($type=='full'){
            return date("Y-m-d , H:i:s",$sTime);
        }elseif($type=='ymd'){
            return date("Y-m-d",$sTime);
        }else{
            if( $dTime < 60 ){
                return $dTime.lang('_SECONDS_AGO_');
            }elseif( $dTime < 3600 ){
                return intval($dTime/60).lang('_MINUTES_AGO_');
            }elseif( $dTime >= 3600 && $dDay == 0  ){
                return intval($dTime/3600).lang('_HOURS_AGO_');
            }elseif($dYear==0){
                return date("Y-m-d H:i:s",$sTime);
            }else{
                return date("Y-m-d H:i:s",$sTime);
            }
        }
    }
}

if (!function_exists('datetime')) {

    /**
     * 将时间戳转换为日期时间
     * @param int $time 时间戳
     * @param string $format 日期时间格式
     * @return string
     */
    function datetime($time, $format = 'Y-m-d H:i:s')
    {
        $time = is_numeric($time) ? $time : strtotime($time);
        return date($format, $time);
    }

}

function get_time_ago($type = 'second', $some = 1, $time = null)
{
    $time = empty($time) ? time() : $time;
    switch ($type) {
        case 'second':
            $result = $time - $some;
            break;
        case 'minute':
            $result = $time - $some * 60;
            break;
        case 'hour':
            $result = $time - $some * 60 * 60;
            break;
        case 'day':
            $result = strtotime('-' . $some . ' day', $time);
            break;
        case 'week':
            $result = strtotime('-' . ($some * 7) . ' day', $time);
            break;
        case 'month':
            $result = strtotime('-' . $some . ' month', $time);
            break;
        case 'year':
            $result = strtotime('-' . $some . ' year', $time);
            break;
        default:
            $result = $time - $some;
    }
    return $result;
}

function get_time_unit($key = null){

    $array = array(
        'second' => lang('_TIME_SECOND_'), 
        'minute' => lang('_TIME_MINUTE_'), 
        'hour' => lang('_HOUR_'), 
        'day' => lang('_TIME_DAY_'), 
        'week' => lang('_TIME_WEEK_'), 
        'month' => lang('_TIME_MONTH_'), 
        'year' => lang('_TIME_YEAR_')
    );
    return empty($key)?$array:$array[$key];
}