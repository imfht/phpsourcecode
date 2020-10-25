<?php
/**
 * Some useful extend tools for PHP
 */
namespace zendforum\Phplus;

use zendforum\Phplus\Int_;

/**
 * for Time
 */
class Time {

    //计时器:标识
    private static $timerMark = [];

    //计时器:计时精度(小数点后位数),支持最大值:6
    private static $timerPrecision = 2;

    /**
     * 获取当前时间,单位:毫秒(ms)
     * @param int $precision 精确到小数点后的位数
     * @return float
     */
    public static function microtime ($precision = 0) {
        $precision = Int_::is_id($precision) ? $precision + 0 : 0;
        $precision = ($precision <= 6) ? $precision : 6;

        $precision_v = pow(10, $precision);
        $microtime = intval(microtime(true) * 1000 * $precision_v);

        return number_format($microtime / $precision_v, $precision, '.', '');
    }

    //计时器:开始
    public static function timerStart ($mark = '') {
        if (!empty($mark) && (is_string($mark) || is_numeric($mark))) self::$timerMark[$mark]['start'] = self::microtime(self::$timerPrecision + 1);
        else self::$timerMark[]['start'] = self::microtime(self::$timerPrecision + 1);
    }

    //计时器:结束
    public static function timerStop ($mark = '') {
        if (!empty($mark) && is_string($mark)) self::$timerMark[$mark]['stop'] = self::microtime(self::$timerPrecision + 1);
        else self::$timerMark[]['stop'] = self::microtime(self::$timerPrecision + 1);
    }

    //计时器:获取计时,单位:ms
    public static function timer ($mark = '') {
        if (!empty($mark) && is_string($mark)) {
            $timerStart = isset(self::$timerMark[$mark]['start']) ? self::$timerMark[$mark]['start'] : 0;
            $timerStop = isset(self::$timerMark[$mark]['stop']) ? self::$timerMark[$mark]['stop'] : 0;

            return round($timerStop - $timerStart, self::$timerPrecision);
        }

        $timer = [];
        if (!empty(self::$timerMark) && is_array(self::$timerMark)) {
            foreach (self::$timerMark as $k => $timerMark) {
                if (empty($timerMark['start'])) continue;
                if (empty($timerMark['stop'])) $timerMark['stop'] = self::microtime(self::$timerPrecision + 1);

                $timer[$k] = round($timerMark['stop'] - $timerMark['start'], self::$timerPrecision);
            }
        }

        return $timer;
    }

    //计时器:重置
    public static function timerReset () {
        self::$timerMark = [];
        self::$timerPrecision = 2;
    }

}
