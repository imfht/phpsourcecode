<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/4/24
 * Time: 8:54
 */

namespace app\admin\model;

use think\Model;

class Column extends Model{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;

    protected static $_field=[
        'id','fid','title','name','keywords','description', 'banner','image','ico','frcolor',
        'bgcolor','position','url','type','status','sort','update_time'
    ];

    /**
     * 获取用户
     * @param $data
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getColumn($data)
    {
        return is_array($data)?self::field(self::$_field)
            ->where($data)->find():self::field(self::$_field)->find($data);
    }

    public static function all($data = null, $with = [], $cache = false)
    {
        return self::field(self::$_field)->where($data)->with($with)->cache($cache)->select();
    }

    protected function getTypeAttr($value){
        $type = [ '未知','列表页',  '下载页', '单页面',  '封面页', '表单页', '跳转页'];
        return $type[$value];
    }
    protected function getFidAttr($value){
        return $value==0?'顶级栏目':$value;
    }
    //栏目位置：1头部，2中部，3左侧，4右侧，5底部
    protected function getPositionAttr($value){
        $type = [ '未知','头部',  '中部', '左侧',  '右侧', '底部'];
        return $type[$value];
    }

    protected function getStatusAttr($value){
        $type = ['正常','禁用'];
        return $type[$value];
    }
}