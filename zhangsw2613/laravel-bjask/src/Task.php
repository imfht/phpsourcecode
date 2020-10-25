<?php
/**
 * 任务基类
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/12/13
 * Time: 17:30
 */

namespace Bjask;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

abstract class Task implements Runnable
{

    use Plan;

    /**
     * 重试次数
     * @var int
     */
    private $tries = 0;
    /**
     * 开始运行时间
     * @var string
     */
    private $startRunTime = '';

    /**
     * 运行状态 0：未执行，1执行中，2已完成，3运行错误
     * @var int
     */
    private $runStatus = 0;

    /**
     * 运行消息
     * @var string
     */
    private $runMessage = '开始运行时间：%s,错误信息：%s,结束运行时间：%s';

    /**
     * 设置执行信息
     * @param $message 消息内容
     * @param int $status
     */
    public function setMessage($message, $status = 0)
    {
        Log::info($message);
        $this->runMessage = sprintf($this->runMessage, $this->startRunTime, $message, date('Y-m-d H:i:s'));
        $this->runStatus = $status;
    }

    /**
     * 获取执行消息
     * @return string
     */
    public function getMessage()
    {
        return $this->runMessage;
    }

    /**
     * 判断任务是否到期
     * @return bool
     */
    public function isDue()
    {
        $currentTime = Carbon::now();
        if (empty($this->currentTime)) $this->currentTime = $currentTime->toDateTimeString();
        $nextRunTime = $currentTime->copy();
        $nextRunTime->setTimeFromTimeString($this->currentTime);
        $currentTime->addSeconds(1);
        foreach ($this->place as $position => $part) {
            if (is_null($part)) continue;
            switch ($position) {
                case 0:
                    $this->handleMonth($nextRunTime);
                    break;
                case 1:
                    $this->handleWeek($nextRunTime);
                    break;
                case 2:
                    $this->handleDay($nextRunTime);
                    break;
                case 3:
                    $this->handleHour($nextRunTime);
                    break;
                case 4:
                    $this->handleMinute($nextRunTime);
                    break;
                case 5:
                    $this->handleSecond($nextRunTime);
                    break;
            }
        }
        if ($currentTime->getTimestamp() == $nextRunTime->getTimestamp()) {
            $this->currentTime = '';
            return true;
        }
        return false;
    }

    /**
     * 设置重试次数
     * @param int|string $tries
     * @return $this
     */
    public function setTries($tries = '')
    {
        if (is_int($tries)) {
            $this->tries = $tries;
        } else {
            ++$this->tries;
        }
        return $this;
    }

    /**
     * 获取重试次数
     * @return int
     */
    public function getTries()
    {
        return $this->tries;
    }

    /**
     * 设置运行状态
     * @param int $status
     * @return $this
     */
    public function setRunStatus($status = 0)
    {
        $this->runStatus = $status;
        return $this;
    }

    /**
     * 获取运行状态
     * @return int
     */
    public function getRunStatus()
    {
        return $this->runStatus;
    }

    public function setStartRunTime()
    {
        $this->startRunTime = time();
    }

    public function getStartRunTime()
    {
        return $this->startRunTime;
    }

}