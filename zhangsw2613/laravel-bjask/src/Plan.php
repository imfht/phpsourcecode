<?php
/**
 * 制定任务计划
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/12/25
 * Time: 16:17
 */

namespace Bjask;

use Illuminate\Support\Carbon;

trait Plan
{

    private static $m = 0;
    private static $w = 1;
    private static $d = 2;
    private static $h = 3;
    private static $i = 4;
    private static $s = 5;
    private $place = [];

    /**
     * 当前时间
     * @var string
     */
    public $currentTime = '';

    /**
     * 间隔时间，单位秒
     * @var int
     */
    public $interval = 0;

    public $isRepeat = true;

    public function everySecond(int $second)
    {
        $this->place[self::$s] = $second;
    }

    public function everyMinute(int $minute)
    {
        $this->place[self::$i] = $minute;
    }

    public function everyHour(int $hour)
    {
        $this->place[self::$h] = $hour;
    }

    public function everyDay(int $day)
    {
        $this->place[self::$d] = $day;
    }

    public function everyWeek(int $week)
    {
        $this->place[self::$w] = $week;
    }

    public function everyMonth(int $month)
    {
        $this->place[self::$m] = $month;
    }

    public function handleMonth(Carbon $nextRunTime)
    {
        $months = $this->place[self::$m];
        $nextRunTime->setDateTime($nextRunTime->format('Y'), $nextRunTime->format('m'), 1, 0, 0);
        $nextRunTime->addMonths($months - 1);
        $nextRunTime->subDays(1);
    }

    public function handleWeek(Carbon $nextRunTime)
    {
        $weeks = $this->place[self::$w];
        $nextRunTime->setTime(0, 0, 0);
        $nextRunTime->subDays($nextRunTime->format('N'));
        $nextRunTime->addWeeks($weeks);
    }

    public function handleDay(Carbon $nextRunTime)
    {
        $days = $this->place[self::$d];
        $nextRunTime->setTime(0, 0, 0);
        $nextRunTime->addDays($days);
    }

    public function handleHour(Carbon $nextRunTime)
    {
        $hours = $this->place[self::$h];
        $nextRunTime->setTime($nextRunTime->format('H'), 0, 0);
        $nextRunTime->addHours($hours);
    }

    public function handleMinute(Carbon $nextRunTime)
    {
        $minutes = $this->place[self::$i];
        $nextRunTime->setTime($nextRunTime->format('H'), $nextRunTime->format('i'), 0);
        $nextRunTime->addMinutes($minutes);
    }

    public function handleSecond(Carbon $nextRunTime)
    {
        $seconds = $this->place[self::$s];
        $nextRunTime->addSeconds($seconds);
    }
}