<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.sycit.cn
// +----------------------------------------------------------------------
// | Author: Peter.Zhang  <hyzwd@outlook.com>
// +----------------------------------------------------------------------
// | Date:   2017/10/17
// +----------------------------------------------------------------------
// | Title:  StorageCharge.php
// +----------------------------------------------------------------------
namespace app\index\model;

use think\Model;

class StorageCharge extends Model
{
    protected $readonly = ['lxname']; // 锁定名为只读
    protected $insert = ['status'=>1]; // 新增数据自动添加字段
    protected $autoWriteTimestamp = true; // 自动写入时间戳

    //添加员
    public function getLxUidAttr($value) {
        $Users = new Users();
        $result = $Users::get($value);
        return $result['user_nick'];
    }
}