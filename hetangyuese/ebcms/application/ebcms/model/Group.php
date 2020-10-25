<?php
namespace app\ebcms\model;

use think\Model;

class Group extends Model
{

    protected $name = 'auth_group';
    protected $pk = 'id';

    public function manager()
    {
        return $this->belongsToMany('Manager', 'auth_access', 'uid', 'group_id');
    }

}