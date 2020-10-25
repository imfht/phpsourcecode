<?php
/* 2017年2月18日 星期六 任务提醒
 *
 */
namespace app\common\model;
use app\common\model\BaseModel;
class Task extends BaseModel{
    protected $table = 'sys_taskrpt';
    protected $pk = 'listno';    
}