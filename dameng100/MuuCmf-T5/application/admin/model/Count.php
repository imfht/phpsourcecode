<?php
namespace app\admin\Model;

use think\Model;
use think\Db;

class Count extends Model
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 每日执行统计计划任务
     * @return bool
     */
    public function dayCount()
    {
        $map['date'] = strtotime(time_format(time(),'Y-m-d 00:00')." - 1 day");
        //if(!Db::name('count_lost')->where($map)->find()){
            //流失率统计
            model('admin/CountLost')->lostCount();
            //留存率统计
            model('admin/CountRemain')->remainCount();
            //活跃用户统计
            model('admin/CountActive')->activeCount();
        //}
        return true;
    }

} 