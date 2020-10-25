<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.sycit.cn
// +----------------------------------------------------------------------
// | Author: Peter.Zhang  <hyzwd@outlook.com>
// +----------------------------------------------------------------------
// | Date:   2017/9/20
// +----------------------------------------------------------------------
// | Title:  Finance.php
// +----------------------------------------------------------------------
namespace app\index\model;

use think\Model;

class Finance extends Model
{
    protected $readonly = ['fpnumber']; // 锁定订单号为只读
    protected $insert = ['status'=>1]; // 新增数据自动添加字段
    protected $autoWriteTimestamp = true; // 自动写入时间戳

    //定义订单号/企业名称/联系人查询
    protected function scopePnumber($query, $val) {
        $query->where('fpnumber|fcus_name', 'like', '%' .$val.'%');
    }

    //获取收款项目
    public function getSortAttr($value) {
        $model = new FinanceSort();
        $result = $model::get($value);
        return $result;
    }

    //获取客户信息
    public function getFcusIdAttr($value) {
        $model = new Customers();
        $resule = $model::get($value);
        return $resule;
    }

    //获取用户信息
    public function getFuidAttr($value) {
        $model = new Users();
        $result = $model::get($value);
        return $result['user_nick'];
    }

    //一对一关联附表
    public function schedule() {
        return $this->hasOne('FinanceSchedule', 'fs_fid');
    }
}