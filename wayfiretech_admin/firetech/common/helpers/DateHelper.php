<?php
/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-15 16:10:15
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-05 16:12:43
 */
 

namespace common\helpers;


class DateHelper
{
    /**
     * 获取今日开始时间戳和结束时间戳
     *
     * 语法：mktime(hour,minute,second,month,day,year) => (小时,分钟,秒,月份,天,年)
     */
    public static function today()
    {
        return [
            'start' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
            'end' => mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1,
        ];
    }

    /**
     * 昨日
     *
     * @return array
     */
    public static function yesterday()
    {
        return [
            'start' => mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')),
            'end' => mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1,
        ];
    }

    /**
     * 这周
     *
     * @return array
     */
    public static function thisWeek()
    {
        $length = 0;
        // 星期天直接返回上星期，因为计算周围 星期一到星期天，如果不想直接去掉
        if (date('w') == 0) {
            $length = 7;
        }

        return [
            'start' => mktime(0, 0, 0, date('m'), date('d') - date('w') + 1 - $length, date('Y')),
            'end' => mktime(23, 59, 59, date('m'), date('d') - date('w') + 7 - $length, date('Y')),
        ];
    }

    /**
     * 上周
     *
     * @return array
     */
    public static function lastWeek()
    {
        $length = 7;
        // 星期天直接返回上星期，因为计算周围 星期一到星期天，如果不想直接去掉
        if (date('w') == 0) {
            $length = 14;
        }

        return [
            'start' => mktime(0, 0, 0, date('m'), date('d') - date('w') + 1 - $length, date('Y')),
            'end' => mktime(23, 59, 59, date('m'), date('d') - date('w') + 7 - $length, date('Y')),
        ];
    }

    /**
     * 本月
     *
     * @return array
     */
    public static function thisMonth()
    {
        return [
            'start' => mktime(0, 0, 0, date('m'), 1, date('Y')),
            'end' => mktime(23, 59, 59, date('m'), date('t'), date('Y')),
        ];
    }

    /**
     * 上个月
     *
     * @return array
     */
    public static function lastMonth()
    {
        $start = mktime(0, 0, 0, date('m') - 1, 1, date('Y'));
        $end = mktime(23, 59, 59, date('m') - 1, date('t'), date('Y'));

        if (date('m', $start) != date('m', $end)) {
            $end -= 60 * 60 * 24;
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * 某一天
     *
     * @param [type] $day
     * @return void
     */
    public static function dayAgo($day){
        if(!$day) return false;
        return [
            'start' => mktime(0, 0, 0, date('m',$day), date('d',$day), date('Y')),
            'end' => mktime(23, 59, 59,  date('m',$day), date('d',$day), date('Y')),
        ];
    }

    /**
     * 几个月前
     *
     * @param integer $month 月份
     * @return array
     */
    public static function monthsAgo($month)
    {
        return [
            'start' => mktime(0, 0, 0, date('m') - $month, 1, date('Y')),
            'end' => mktime(23, 59, 59, date('m') - $month, date('t'), date('Y')),
        ];
    }

    /**
     * 某年
     *
     * @param $year
     * @return array
     */
    public static function aYear($year)
    {
        $start_month = 1;
        $end_month = 12;

        $start_time = $year . '-' . $start_month . '-1 00:00:00';
        $end_month = $year . '-' . $end_month . '-1 23:59:59';
        $end_time = date('Y-m-t H:i:s', strtotime($end_month));

        return [
            'start' => strtotime($start_time),
            'end' => strtotime($end_time)
        ];
    }

    /**
     * 某月
     *
     * @param int $year
     * @param int $month
     * @return array
     */
    public static function aMonth($year = 0, $month = 0)
    {
        $year = $year ?? date('Y');
        $month = $month ?? date('m');
        $day = date('t', strtotime($year . '-' . $month));

        return [
            "start" => strtotime($year . '-' . $month),
            "end" => mktime(23, 59, 59, $month, $day, $year)
        ];
    }

    /**
     * @param int $time
     * @param string $format
     * @return mixed
     */
    public static function getWeekName(int $time, $format = "周")
    {
        $week = date('w', $time);
        $weekname = ['日', '一', '二', '三', '四', '五', '六'];
        foreach ($weekname as &$item) {
            $item = $format . $item;
        }

        return $weekname[$week];
    }

    /**
     * 格式化时间戳
     *
     * @param $time
     * @return string
     */
    public static function formatTimestamp($time)
    {
        $min = $time / 60;
        $hours = $time / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));

        return $days . " 天 " . $hours . " 小时 " . $min . " 分钟 ";
    }

    /**
     * 时间戳
     *
     * @param integer $accuracy 精度 默认微妙
     * @return int
     */
    public static function microtime($accuracy = 1000)
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * $accuracy);

        return $msectime;
    }

            /**
         * convert unix timestamp to ISO 8601 compliant date string
         *
         * @param    int $timestamp Unix time stamp
         * @param    boolean $utc Whether the time stamp is UTC or local
         * @return    mixed ISO 8601 date string or false
         * @access   public
         */
        public static  function timestamp_to_iso8601($timestamp, $utc = true)
        {
            $datestr = date('Y-m-d\TH:i:sO', $timestamp);
            $pos = strrpos($datestr, "+");
            if ($pos === false) {
                $pos = strrpos($datestr, "-");
            }
            if ($pos !== false) {
                if (strlen($datestr) == $pos + 5) {
                    $datestr = substr($datestr, 0, $pos + 3) . ':' . substr($datestr, -2);
                }
            }
            if ($utc) {
                $pattern = '/' .
                    '([0-9]{4})-' .    // centuries & years CCYY-
                    '([0-9]{2})-' .    // months MM-
                    '([0-9]{2})' .    // days DD
                    'T' .            // separator T
                    '([0-9]{2}):' .    // hours hh:
                    '([0-9]{2}):' .    // minutes mm:
                    '([0-9]{2})(\.[0-9]*)?' . // seconds ss.ss...
                    '(Z|[+\-][0-9]{2}:?[0-9]{2})?' . // Z to indicate UTC, -/+HH:MM:SS.SS... for local tz's
                    '/';

                if (preg_match($pattern, $datestr, $regs)) {
                    return sprintf('%04d-%02d-%02dT%02d:%02d:%02dZ', $regs[1], $regs[2], $regs[3], $regs[4], $regs[5], $regs[6]);
                }
                return false;
            } else {
                return $datestr;
            }
        }

        /**
        * convert ISO 8601 compliant date string to unix timestamp
        *
        * @param    string $datestr ISO 8601 compliant date string
        * @return    mixed Unix timestamp (int) or false
        * @access   public
        */
        public static  function iso8601_to_timestamp($datestr)
        {
            $pattern = '/' .
                '([0-9]{4})-' .    // centuries & years CCYY-
                '([0-9]{2})-' .    // months MM-
                '([0-9]{2})' .    // days DD
                'T' .            // separator T
                '([0-9]{2}):' .    // hours hh:
                '([0-9]{2}):' .    // minutes mm:
                '([0-9]{2})(\.[0-9]+)?' . // seconds ss.ss...
                '(Z|[+\-][0-9]{2}:?[0-9]{2})?' . // Z to indicate UTC, -/+HH:MM:SS.SS... for local tz's
                '/';
            if (preg_match($pattern, $datestr, $regs)) {
                // not utc
                if ($regs[8] != 'Z') {
                    $op = substr($regs[8], 0, 1);
                    $h = substr($regs[8], 1, 2);
                    $m = substr($regs[8], strlen($regs[8]) - 2, 2);
                    if ($op == '-') {
                        $regs[4] = $regs[4] + $h;
                        $regs[5] = $regs[5] + $m;
                    } elseif ($op == '+') {
                        $regs[4] = $regs[4] - $h;
                        $regs[5] = $regs[5] - $m;
                    }
                }
                return gmmktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
        //		return strtotime("$regs[1]-$regs[2]-$regs[3] $regs[4]:$regs[5]:$regs[6]Z");
            } else {
                return false;
            }
        }

        /**
         * sleeps some number of microseconds
         *
         * @param    string $usec the number of microseconds to sleep
         * @access   public
         * @deprecated
         */
        public static  function usleepWindows($usec)
        {
            $start = gettimeofday();

            do {
                $stop = gettimeofday();
                $timePassed = 1000000 * ($stop['sec'] - $start['sec'])
                    + $stop['usec'] - $start['usec'];
            } while ($timePassed < $usec);
        }
}