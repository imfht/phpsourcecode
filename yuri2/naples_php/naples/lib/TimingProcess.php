<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2017/1/3
 * Time: 21:14
 */

namespace naples\lib;


use naples\lib\base\Service;

/** 
 * 用来定时检测是否该需要执行某项任务
 */
class TimingProcess extends Service
{
    const ARR_DB_NAME='/sys/Timings';
    /** @var  ArrData $timings */
    private $timings;
    private $data=[];
    private $tasks=[];

    /** 初始化 */
    function init(){
        $arrDbName=self::ARR_DB_NAME;
        $timings=Factory::getArrDatabase($arrDbName);
        $timings->load();
        $this->timings=$timings;
        $this->data=&$this->timings->data;
        $this->tasks=$this->config();
        $this->checkAndDo();
        $this->timings->save();
    }
    
    /** 
     * 检查并执行任务
     */
    private function checkAndDo(){
        foreach ($this->tasks as $task => $func){
            $task_data=isset($this->data[$task])?$this->data[$task]:null;
            $task_rel=$func($task_data);
            $this->data[$task]=$task_rel;
        }
    }
}