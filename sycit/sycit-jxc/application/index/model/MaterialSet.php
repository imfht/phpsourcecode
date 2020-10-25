<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.sycit.cn
// +----------------------------------------------------------------------
// | Author: Peter.Zhang  <hyzwd@outlook.com>
// +----------------------------------------------------------------------
// | Date:   2017/12/1
// +----------------------------------------------------------------------
// | Title:  MaterialSet.php
// +----------------------------------------------------------------------
namespace app\index\model;

use think\Model;

class MaterialSet extends Model
{
    protected $insert = ['status'=>1]; // 新增数据自动添加字段
    protected $autoWriteTimestamp = true; // 自动写入时间戳

    //获取收款项目
    public function getMsUidAttr($value) {
        $model = new Users();
        $result = $model::get($value);
        return $result['user_nick'];
    }
}