<?php
namespace app\Finance\model;
class Fincset extends \think\Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'finc_set';
    // 设置数据表主键
    protected $pk = 'finc_no';
    // 设置当前数据表的字段信息
    protected $field = [
        'finc_no',
        'center_id'
    ];
}