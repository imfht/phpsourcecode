<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29
 * Time: 11:50
 */

namespace app\admin\model;


use think\Db;
use think\Model;

class CronTaskModel extends Model
{

    public function getBelongCateName($cron_task_id){
        $cate_id = $this->where('id',$cron_task_id)->value('cate_id');
        return Db::name('cate')->where('id',$cate_id)->value('name');
    }
}