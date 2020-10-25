<?php
namespace app\admin\model;

use think\Model;

class AdminBehaviorLog extends Model{
    protected $table = 'think_admin_behavior_log';
    // protected $pk = 'id';
    // 不起做用
    // protected $insert = ['password'];
    // public function setNameAttr($value)
    // {
    //     return md5($value);
    // }
    // public $date = NOW_TIME;
    // protected $auto = array(
    //     'ip' => '123',
    //     'create_time' => NOW_TIME,
    //     'haha' => 'xx',
    // );
}