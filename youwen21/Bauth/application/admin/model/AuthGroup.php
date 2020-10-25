<?php

namespace app\admin\model;

use think\Model;

class AuthGroup extends Model{

    // protected $auto = ['update_time'];
    // protected $insert = ['create_time','rules'];

    protected function setRulesAttr()
    {
        $resource = input('post.resource/a') ? implode(',',input('post.resource/a')) : '';
        if(!$resource){
            $resource = '1,6,11';
        }
        return $resource;
    }

    public static function groupUsers($group_id)
    {
        $sql = "SELECT * FROM think_auth_group_access as ac,think_administrator as u WHERE ac.uid=u.id AND ac.group_id=$group_id";
        $users = db('')->query($sql);
        return $users;
    }
}