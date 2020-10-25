<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29
 * Time: 17:32
 */

namespace app\common\cron;


use think\Exception;

class CronExpression
{

    /**
     * @param $str_cron
     * @throws Exception
     * 获取下次时间
     */
    public static function getNextRunTime($str_cron,$timestamp = 0){

        $timestamp = $timestamp > 0 ? $timestamp : time();
        $cron_arr = self::formatCron($str_cron);
        //dump($cron_arr);
        $satisfied_weeks = $cron_arr[5];

        $year = date('Y',$timestamp);
        $now = date('Y-m-d',$timestamp);

        $satisfied_dates = self::getSatisfiedDates($year,$cron_arr[4],$cron_arr[3],$satisfied_weeks);

        $next_satisfied_date = '';
        //今年找不到符合条件月日，则明年找
        if(empty($satisfied_dates)){
            $satisfied_dates = self::getSatisfiedDates($year+1,$cron_arr[4],$cron_arr[3],$satisfied_weeks);
            $next_satisfied_date = $satisfied_dates[0];
            return $next_satisfied_date . ' ' . self::_fillZero($cron_arr[2][0]) . ':'. self::_fillZero($cron_arr[1][0]) . ':'. self::_fillZero($cron_arr[0][0]);
        }
        if(count($satisfied_dates) == 1){
            $tmp_satisfied_dates = self::getSatisfiedDates($year+1,$cron_arr[4],$cron_arr[3],$satisfied_weeks);
            $next_satisfied_date = $tmp_satisfied_dates[0];
        }
        $satisfied_date = $satisfied_dates[0];

        $next_satisfied_date = isset($satisfied_dates[1]) ? $satisfied_dates[1] : $next_satisfied_date;


        //如果日期比现在大，那么时分秒直接取最小的
        if($satisfied_date > $now){

            $str=  $satisfied_date . ' ' . self::_fillZero($cron_arr[2][0]) . ':'. self::_fillZero($cron_arr[1][0]) . ':'. self::_fillZero($cron_arr[0][0]);
            return $str;
        }
        //如果日期与当前相同，那么再比较时分秒
        $satisfied_hours = $cron_arr[2];
        $satisfied_minutes = $cron_arr[1];
        $satisfied_seconds = $cron_arr[0];

        //如果当前时间已经最大
        $max_time = self::_fillZero(max($satisfied_hours)) . ':' . self::_fillZero(max($satisfied_minutes)). ':'.  self::_fillZero(max($satisfied_seconds));

        if(date('H:i:s',$timestamp) >= $max_time){
            return $next_satisfied_date . ' ' . self::_fillZero($cron_arr[2][0]) . ':'. self::_fillZero($cron_arr[1][0]) . ':'. self::_fillZero($cron_arr[0][0]);
        }
        //时分秒里面找出比当前大的最小的即可
        $hour = date('H',$timestamp);
        $hours = [];
        foreach ($satisfied_hours as $val){
            if($val >= $hour){
                $hours[] = $val;
            }
            if(count($hours)>=2){
                break;
            }
        }
        $time = date('H:i',$timestamp);
        $times = [];
        foreach ($hours as $val){
            foreach ($satisfied_minutes as $min){
                $str = self::_fillZero($val).':'.self::_fillZero($min);
                if($str >= $time){
                    $times[] = $str;
                }
                if(count($times)>=2){
                    break;
                }
            }
        }
        $time = date('H:i:s',$timestamp);
        foreach ($times as $val){
            foreach ($satisfied_seconds as $sec){
                $str = $val.':'.self::_fillZero($sec);
                if($str > $time){
                    return $satisfied_date . ' '.$str;
                }
            }
        }


    }

    private static function getSatisfiedDates($year,$months,$days,$satisfied_weeks){
        $now = date('Y-m-d');
        $satisfied_dates = [];
        foreach ($months as $month){
            foreach ($days as $day){
                $date = $year . '-' . self::_fillZero($month) . '-' . self::_fillZero($day);
                $week = date('w',strtotime($date));
                if($date >= $now && in_array($week,$satisfied_weeks)){
                    $satisfied_dates[] = $date;
                }
                if(count($satisfied_dates) >= 2){
                    return $satisfied_dates;
                }
            }
        }
        return $satisfied_dates;
    }


    private static function _fillZero($number){
        if(strlen($number) == 2) return $number;
        return $number >= 0 && $number <= 9 ? '0' . $number : $number;
    }

    /**
     * @param $str_cron
     * @return array
     * @throws Exception
     */
    public static function formatCron($str_cron){

        $str_cron = trim($str_cron);

        $cron_arr = [];
        $parts = array_values(
            array_filter(explode(' ',$str_cron),function($var){
            if($var != ''){
                return true;
            }
        }));
        $cron_arr[0] = self::parsePart($parts[0],0,59); //秒
        $cron_arr[1] = self::parsePart($parts[1],0,59); //分
        $cron_arr[2] = self::parsePart($parts[2],0,23); //时
        $cron_arr[3] = self::parsePart($parts[3],1,31); //日
        $cron_arr[4] = self::parsePart($parts[4],1,12); //月
        $cron_arr[5] = self::parsePart($parts[5],0,6); //星期

        return $cron_arr;

    }

    /**
     * @param $part
     * @param $min
     * @param $max
     * @return array
     * @throws Exception
     */
    public static function parsePart($part,$min,$max){
        $list = [];
        //包含","
        if(strpos($part,',') !== false){
            $arr = explode(',', $part);
            foreach ($arr as $v){
                $tmp = self::parsePart($v,$min,$max);
                $list = array_merge($list,$tmp);
            }
            return $list;
        }

        //包含"/"
        $tmp = explode('/',$part);
        $part = $tmp[0];
        // 斜杆后面一位就是步长
        $step = isset($tmp[1]) ? $tmp[1] : 1;

        //包含"-"
        if(strpos($part,'-') !== false){
            list($tmp_min,$tmp_max) = explode('-',$part);
            if($tmp_min >= $tmp_max){
                throw new Exception('使用"-"设置范围时，左不能大于右');
            }
        }elseif($part == '*' || $part == '?'){
            $tmp_min = $min;
            $tmp_max = $max;
        }else{
            $tmp_min = $tmp_max = $part;
        }

        if($min == $tmp_min && $max == $tmp_max && $step == 1){
            return range($min,$max);
        }
        //越界判断
        if ($tmp_min < $min || $tmp_max > $max) {
            throw new Exception('数值越界。应该：秒0-59，分0-59，时0-59，日1-31，月1-12，周0-6');
        }

        return $tmp_max-$tmp_min > $step ? range($tmp_min,$tmp_max,$step) : [(int)$tmp_min];

    }


}