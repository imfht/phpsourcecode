<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.sycit.cn
// +----------------------------------------------------------------------
// | Author: Peter.Zhang  <hyzwd@outlook.com>
// +----------------------------------------------------------------------
// | Date:   2017/11/19
// +----------------------------------------------------------------------
// | Title:  StockpileLock.php
// +----------------------------------------------------------------------
namespace app\index\model;

use think\Model;

class StockpileLock extends Model
{
    protected $insert = ['status'=>1]; // 新增数据自动添加字段
    protected $autoWriteTimestamp = true; // 自动写入时间戳

    //锁具名称
    public function getStLidAttr($vaule) {
        $model = new FittingsLock();
        $result = $model::get($vaule);
        return $result;
    }
}