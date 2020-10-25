<?php
namespace app\common\model;


use utils\UUID;
use think\Db;
use think\Model;
use app\common\dao\UserInterface;

class UserModel extends Model implements UserInterface
{
    protected $name  = 'sys_user';
    protected $alias = 'su';
    protected $readonly = 'su.created_at';
    protected $field =
                   'su.id,
                    su.name,
                    su.email,
                    su.phone,
                    su.introduction,
                    su.sex,
                    su.birth_at,
                    su.real_name,
                    su.id_name,
                    su.location,
                    su.remember_token,
                    su.created_at,
                    su.updated_at';

    public function getFind($data)
    {
        switch ($data['type']) {
            case 1:
                $name = 'name';
                break;
            case 2:
                $name = 'email';
                break;
            case 3:
                $name = 'phone';
                break;
            default:
                return false;
        }
        $user = Db::table('sys_user su')
            ->field($this->field)
            ->where([$name => $data['username'], 'password' => $data['password'], 'status'=>1])->find();
        if(!empty($user)){
            return $user;
        }
        return false;
    }

    public function getAllList()
    {
        $data = Db::table('sys_user su')
            ->field($this->field)
            ->select();
        return $data;
    }
    public function getAllListByDate($user_id){}
    public function insertDate($data)
    {
        $data['id'] = UUID::uuid();
        $times = date("Y-m-d H:i:s",time());
        $data['created_at'] = $times;
        $data['updated_at'] = $times;
        return Db::table('sys_user')->insertGetId($data);
    }
    public function saveDate($data)
    {
        $data['updated_at'] = date("Y-m-d H:i:s",time());
        Db::table('sys_user')->update($data);
    }
    public function updateBuild($data){}
    public function deleteDate($id)
    {
        Db::table('sys_user')->where('id',$id)->delete();
    }
    public function deleteOnDate($data){}
    public function getDetail($user_id)
    {
        $user = Db::table('sys_user')
            ->where('id', $user_id)->find();
        return $user;
    }

}