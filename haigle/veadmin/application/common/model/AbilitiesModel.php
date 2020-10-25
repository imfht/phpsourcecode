<?php
namespace app\common\model;


use utils\UUID;
use think\Db;
use think\Exception;
use think\exception\DbException;
use think\exception\PDOException;
use think\Model;
use app\common\dao\AbilitiesInterface;

class AbilitiesModel extends Model implements AbilitiesInterface
{
    protected $table = 'sys_abilities';
    protected $alias = 'sa';
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    protected $readonly = 'created_at';
    protected $fields = ['sa.id',
                         'sa.parent_id',
                         'sa.name',
                         'sa.sort',
                         'sa.icon',
                         'sa.href',
                         'sa.permission',
                         'sa.is_show',
                         'sa.created_at',
                         'sa.updated_at'];

    public function getAllList()
    {
        $data = Db::table('sys_abilities sa')
                    ->field($this->fields)
                    ->order('sort')
                    ->select();

        return $data;
    }

    //权限&菜单
    public function getAllListByDate($user_id)
    {
        $source_data = Db::table('sys_abilities sa')
                            ->field($this->fields)
                            ->leftJoin('sys_role_abilities sra','sra.abilities_id = sa.id')
                            ->leftJoin('sys_user_role sur','sur.role_id = sra.role_id')
                            ->where('sur.user_id ='. $user_id)
                            ->select();
        return $source_data;
    }

    public function getRoleAbilities($role_id)
    {
        $data = Db::table('sys_role_abilities')
                    ->field('abilities_id')
                    ->where('role_id',$role_id)
                    ->select();
        return $data;
    }

    public function trueAbilities($user_id)
    {
        $data = Db::table('sys_abilities sa')
                    ->field('sa.href')
                    ->leftJoin('sys_role_abilities sra','sra.abilities_id = sa.id')
                    ->leftJoin('sys_user_role sur','sur.role_id = sra.role_id')
                    ->where('sur.user_id ='. $user_id)
                    ->select();
        $ids = array();
        foreach ($data as $datum){
            $ids[] = $datum['href'];
        }
        return $ids;
    }

    public function getFind($data)
    {
        $source_data = Db::table('sys_abilities sa')
                    ->field($this->fields)
                    ->where('id', $data)->find();
        return $source_data;
    }

    public function insertDate($data)
    {
        $data['id'] = UUID::uuid();
        AbilitiesModel::create($data);
    }

    public function saveDate($data)
    {
        Db::table('sys_abilities')->update($data);
    }

    public function updateBuild($data){}

    public function deleteDate($id)
    {
        Db::table('sys_abilities')->delete($id);
    }
    public function deleteOnDate($data){}

}