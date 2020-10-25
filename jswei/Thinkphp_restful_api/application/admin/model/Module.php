<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/4/24
 * Time: 8:54
 */

namespace app\admin\model;

use think\Model;

class Module extends Model{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $auto=[];
    protected static $_field=[ 'id','fid','title','name as `index`','ico as `icon`','info','status','sort','create_time','update_time'];

    /**
     * 获取用户
     * @param $data
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getModule($data)
    {
        return is_array($data)?self::field(self::$_field)
            ->where($data)->find():self::field(self::$_field)->find($data);
    }

    public static function all($data = null, $with = [], $cache = false)
    {
        return self::field(self::$_field)->where($data)->with($with)->cache($cache)->select();
    }

    public function setStatusAttr($value){
        return $value=='正常'?0:1;
    }

    protected function getStatusAttr($value){
        $type = ['正常','禁用'];
        return $type[$value];
    }

    protected function getFidAttr($value){
        return $value;
    }
}