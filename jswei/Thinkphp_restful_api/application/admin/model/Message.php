<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/4/24
 * Time: 8:54
 */

namespace app\admin\model;

use think\Model;

class Message extends Model{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected static $_field=[ 'id','mid','title','content','type','status','update_time'];

    public function setTypeAttr($value){
        $type = ['系统','降价','优惠','其他'];
        return array_keys($type,$value)[0]+1;
    }

    public static function getMessage($data)
    {
        return is_array($data)?self::field(self::$_field)
            ->where($data)->find():self::field(self::$_field)->find($data);
    }

    public static function all($data = null, $with = [], $cache = false)
    {
        return self::field(self::$_field)->where($data)->with($with)->cache($cache)->select();
    }

    protected function getTypeAttr($value){
        $type = ['系统','降价','优惠','其他'];
        return $type[$value-1];
    }

    protected function getStatusAttr($value){
        $type = ['','正常','禁用'];
        return $type[$value];
    }

    public function getMidAttr($value){
        $integerIDs = array_map('intval', explode(',', $value));
        return $integerIDs;
    }
}