<?php
namespace app\common\model;

use think\Model;

/**
 * 定时任务
 * @package app\admin\model
 */
class Timedtask extends Model
{
    protected $table = '__TIMED_TASK__';
	
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $resultSetType = 'array';
}