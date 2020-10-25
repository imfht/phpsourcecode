<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/4/24
 * Time: 8:54
 */

namespace app\first\model;

use think\Model;

class Member extends Model{
    protected $pk = 'id';
    protected $auto = ['last_login_ip','update_time'];
    protected $insert = ['sex' => 0];
    protected $autoWriteTimestamp = true;
    protected  static $_field = ['id as user_id,username,information,sex,nickname,head,tel,hobbies,client_id,secret,email,region,address'];

    /**
     * 获取用户
     * @param $data
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getMember($data)
    {
        return is_array($data)?self::field(self::$_field)
            ->where($data)->find():self::field(self::$_field)->find($data);
    }

    public function getSexAttr($value)
    {
        $sex = [-1=>'未知',0=>'男',1=>'女'];
        return $sex[$value];
    }

    public function getHeadAttr($value){
        if(empty($value)){
            return null;
        }else if(strpos($value,'://')===false){
            return request()->Domain().DIRECTORY_SEPARATOR.substr($value,1);
        }else{
            return $value;
        }
    }

    protected function setLastLoginIpAttr()
    {
        return request()->ip();
    }

    protected function setUpdateTimeAttr(){
        return time();
    }
}