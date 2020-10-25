<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/4/24
 * Time: 8:54
 */

namespace app\admin\model;

use think\Model;

class Carousel extends Model{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected static $_field=[ 'id','title','image','url','keywords','description','status','update_time'];

    public static function getMessage($data)
    {
        return is_array($data)?self::field(self::$_field)
            ->where($data)->find():self::field(self::$_field)->find($data);
    }

    public static function all($data = null, $with = [], $cache = false)
    {
        return self::field(self::$_field)->where($data)->with($with)->cache($cache)->select();
    }

    protected function setUpdateTimeAttr(){
        return time();
    }

    protected function setStatusAttr($value){
        return $value=='正常'?1:2;
    }

    protected function getStatusAttr($value){
        $type = ['','正常','禁用'];
        return $type[$value];
    }
}