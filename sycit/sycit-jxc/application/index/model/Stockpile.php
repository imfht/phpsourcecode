<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.sycit.cn
// +----------------------------------------------------------------------
// | Author: Peter.Zhang  <hyzwd@outlook.com>
// +----------------------------------------------------------------------
// | Date:   2017/10/17
// +----------------------------------------------------------------------
// | Title:  Stockpile.php
// +----------------------------------------------------------------------
namespace app\index\model;

use think\Model;

class Stockpile extends Model
{
    protected $insert = ['status'=>1]; // 新增数据自动添加字段
    protected $autoWriteTimestamp = true; // 自动写入时间戳

    //颜色
    public function getSpPcidAttr($vaule) {
        $model = new ProductColor();
        $result = $model::get($vaule);
        return $result;
    }

    //料型
    public function getSpLxidAttr($vaule) {
        $model = new StorageCharge();
        $result = $model::get($vaule);
        return $result;
    }
}