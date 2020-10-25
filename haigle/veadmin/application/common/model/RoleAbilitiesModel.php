<?php
namespace app\common\model;


use utils\UUID;
use think\Db;
use think\Model;
use app\common\dao\RoleAbilitiesInterface;

class RoleAbilitiesModel extends  Model implements RoleAbilitiesInterface
{
    protected $name  = 'sys_role_abilities';
    protected $alias = 'sra';
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'created_at';
    protected $readonly = 'created_at';
    protected $fields = ['role_id',
                        'abilities_id',
                        'created_at'];

    public function getFind($data){}

    public function getAllList(){}

    public function getAllListByDate($user_id){}

    public function insertDate($data)
    {
        Db::name('sys_role_abilities')->insertAll($data);
    }
    public function saveDate($data){}
    public function updateBuild($data){}
    public function deleteDate($id){}
    public function deleteDateByAbilitiesId($abilities_id){
        Db::table('sys_role_abilities')->where('abilities_id',$abilities_id)->delete();
    }
    public function deleteDateByRoleId($role_id){
        Db::table('sys_role_abilities')->where('role_id',$role_id)->delete();
    }
    public function deleteOnDate($data){}

}