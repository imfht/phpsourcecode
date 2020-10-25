<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/4/24
 * Time: 8:54
 */

namespace app\admin\model;

use think\Model;

class Admin extends Model{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $auto = ['status','code','create_time', 'last_login_ip'];
    protected static $_field = [ 'id','username','password','gid','status','create_time','update_time'];

    public function setCreateTimeAttr(){
        return time();
    }

    public static function getMember($data)
    {
        return is_array($data) ? self::field(self::$_field)
            ->where($data)->find() : self::field(self::$_field)->find($data);
    }

    public static function all($data = null, $with = [], $cache = false)
    {
        return self::field(self::$_field)->where($data)->with($with)->cache($cache)->select();
    }

    public static function pager($data= null, $with = [], $cache = false){
        return self::field(self::$_field)
            ->where($data)
            ->with($with)
            ->cache($cache)
            ->order('id desc')
            ->paginate();
    }

    protected function setPasswordAttr($value){
        return substr($value,10,15);
    }

    public function setLastLoginIpAttr(){
        return request()->ip();
    }

    public function setGidAttr($value){
        $code = implode(',',(array)$value);
        return $code;
    }

    protected function getStatusAttr($value){
        $type = ['正常','禁用'];
        return $type[$value];
    }

    protected function getLastDateAttr($value){
        return date('Y-m-d H:i:s',$value);
    }

    public function getMidAttr($value){
        $integerIDs = array_map('intval', explode(',', $value));
        return $integerIDs;
    }

    public function getCodeAttr($value){
        $code = explode(',',$value);
        return $code;
    }
}