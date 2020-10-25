<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.sycit.cn
// +----------------------------------------------------------------------
// | Author: Peter.Zhang  <hyzwd@outlook.com>
// +----------------------------------------------------------------------
// | Date:   2017/11/8
// +----------------------------------------------------------------------
// | Title:  BancaiList.php
// +----------------------------------------------------------------------
namespace app\index\model;

use think\Model;

class BancaiList extends Model
{
    protected $insert = ['status'=>1]; // 新增数据自动添加字段
    protected $autoWriteTimestamp = true; // 自动写入时间戳

    //添加员
    public function getBlUidAttr($value) {
        $Users = new Users();
        $result = $Users::get($value);
        return $result['user_nick'];
    }

    //产品序列
    public function getBlpnidAttr($value) {
        $model = new ProductNumber();
        $result = $model::get($value);
        return $result;
    }
}