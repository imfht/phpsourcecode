<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.sycit.cn
// +----------------------------------------------------------------------
// | Author: Peter.Zhang  <hyzwd@outlook.com>
// +----------------------------------------------------------------------
// | Date:   2017/9/19
// +----------------------------------------------------------------------
// | Title:  PurchaseAffirm.php
// +----------------------------------------------------------------------
namespace app\index\model;

use think\Model;

class PurchaseAffirm extends Model
{
    protected $readonly = ['a_pnumber']; // 锁定用户名为只读
    protected $insert = ['create_ip']; // 新增数据自动添加字段
    protected $update = ['update_ip']; // 更新数据自动修改字段
    protected $autoWriteTimestamp = true; // 自动写入时间戳

    // 自动添加IP
    protected function setCreateIpAttr()
    {
        return request()->ip();
    }
}