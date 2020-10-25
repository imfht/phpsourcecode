<?php


namespace app\index\model;
use think\Model;

class User extends Model
{
//    设置主键为id
    protected $pk = 'id';
//    设置自动写入时间戳字段
    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = false;
    // 时间字段取出后的默认时间格式
    protected $dateFormat = 'Y-m-d H:i:s';
//    设置自动写入的时间戳字段名
    protected $createTime = 'create_time';
//    实现自动写入登录ip字段
    protected $auto = ['ip','login_time'];
    protected function setIpAttr()
    {
        return request()->ip();
    }
    public function setLoginTimeAttr()
    {
        return date('Y-m-d H:i:s', time());
    }


}