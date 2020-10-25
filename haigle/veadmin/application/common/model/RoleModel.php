<?php
namespace app\common\model;


use utils\UUID;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;
use think\exception\PDOException;
use think\Model;
use app\common\dao\RoleInterface;

class RoleModel extends  Model implements RoleInterface
{
    protected $name  = 'sys_role';
    protected $alias = 'sr';
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    protected $readonly = 'created_at';
    protected $fields = 'sr.id,
                        sr.name,
                        sr.ename,
                        sr.role_type,
                        sr.display_name,
                        sr.description,
                        sr.usable,
                        sr.created_at,
                        sr.updated_at';

    public function getFind($data)
    {
        $source_data = Db::table('sys_role sr')
            ->field($this->fields)
            ->where('id', $data)->find();
        return $source_data;
    }

    public function getAllList()
    {
        try {
            $data = Db::table('sys_role sr')
                ->field($this->fields)
                ->select();
        } catch (DbException $e) {
            return false;
        }
        return $data;
    }

    public function getAllListByDate($user_id)
    {

        $source_data = Db::table('sys_role sr')
                        ->field($this->fields)
                        ->join( 'sys_user_role sur','sur.role_id = sr.id AND sur.user_id ='.'"'.$user_id.'"')
                        ->select();
        return $source_data;
    }

    public function insertDate($data)
    {
        $data['id'] = UUID::uuid();
        RoleModel::create($data);
        return true;
    }
    public function saveDate($data)
    {
        Db::table('sys_role')->update($data);
    }
    public function updateBuild($data){}
    public function deleteDate($id)
    {
        Db::table('sys_role')->where('id',$id)->delete();
    }
    public function deleteOnDate($data){}

}