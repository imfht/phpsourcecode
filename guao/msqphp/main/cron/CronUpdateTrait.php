<?php declare(strict_types = 1);
namespace msqphp\main\cron;

use msqphp\base\file\File;

trait CronUpdateTrait
{
    public static function update(int $begin_time, int $length) : void
    {
        $file = static::getFilePath('cron');
        $end = $begin_time + $length;
        if (is_file($file)) {
            // 只读
            $fp = fopen($file, 'r');
            $all = [];

            do {
                $all[] = array_merge(explode(' ', date('i G j n w', $time)), time());;
            } while ($time + 60 < $end);

            while (false !== $info = fgets($fp)) {
                // 获得单挑信息
                $info = static::readOneInfo($info);
                [$minute, $hour, $day, $month, $week] = explode(' ', $info['time']);
                foreach ($all as $may) {
                    if (!static::passWeek($may[0], $week) ||!static::passMonth($may[1], $month) ||!static::passDay($may[2], $day) ||!static::passHour($may[3], $hour) ||!static::passMinute($may[4], $minute)) {
                        continue;
                    } else {
                        static::add($info['type'], $info['value'], $may[5]);
                    }
                }
            }
        }

        static::add('callback', '\\msqphp\\core\\cron\\Cron@update?(int)'.$end.'&(int)'.$length, $end);
    }
    private static function passCheck(int $may, string $type, int $less, int $most)
    {
        if ($type === '*') {
            return true;
        }
        if (fasle !== strpos($type, '-')) {
            [$min,$max] = explode('-', $type);
            return $min <= $may && $may <= $max;
        } elseif (false !== strpos($type, ',')) {
            $target = explode(',',$type);
            return in_array((string)$may, $target);
        } elseif (false !== strpos('*/')) {
            $target = [];
            $max = $most;
            $inter = (int) substr($type, 2);
            for ($i = $max; $i > 0; --$i) {
                $i % $inter === 0 && $target[] = $i;
            }
            return in_array($may, $target);
        } else {
            return (int) $type === $may;
        }
        return false;
    }
    private static function passWeek(int $may, string $type) : bool
    {
        static::passCheck($may, $type, 0, 6);
    }
    private static function passMonth(int $may, string $type) : bool
    {
        static::passCheck($may, $type, 1, 12);
    }
    private static function passDay(int $may, string $type) : bool
    {
        static::passCheck($may, $type, 1, 31);
    }
    private static function passHour(int $may, string $type) : bool
    {
        static::passCheck($may, $type, 0, 23);
    }
    private static function passMinute(int $may, string $type) : bool
    {
        static::passCheck($may, $type, 0, 59);
    }

}