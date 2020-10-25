<?php
namespace app\common\model;


use think\Db;
use think\Model;
use app\common\dao\UserRoleInterface;

class UserRoleModel extends  Model implements UserRoleInterface
{
    protected $name  = 'sys_user_role';
    protected $alias = 'sur';
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'created_at';
    protected $readonly = 'created_at';
    protected $fields = ['user_id',
                        'role_id',
                        'created_at'];

    public function getFind($data){}

    public function getAllList(){}

    public function getAllListByDate($user_id)
    {
        return Db::table('sys_user_role')->where('user_id',$user_id)->select();
    }

    public function insertDate($data)
    {
        Db::table('sys_user_role')->insertAll($data);
    }
    public function saveDate($data){}
    public function updateBuild($data){}
    public function deleteDate($id){}
    public function deleteDateByUserId($user_id){
        Db::table('sys_user_role')->where('user_id',$user_id)->delete();
    }
    public function deleteDateByRoleId($role_id){
        Db::table('sys_user_role')->where('role_id',$role_id)->delete();
    }
    public function deleteOnDate($data){}

}