<?php
namespace app\ebcms\model;

use think\Model;

class Manager extends Model
{

    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    public function group()
    {
        return $this->belongsToMany('Group', 'auth_access', 'group_id', 'uid');
    }

    protected function setEmailAttr($value)
    {
        return strtolower($value);
    }

    protected function setPasswordAttr($value, $data)
    {
        return \ebcms\Func::crypt_pwd($value, $this -> email);
    }

}