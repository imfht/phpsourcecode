<?php
namespace app\common\fun;

use think\Db;

class Area{
    
    /**
     * 根据地区ID获取地区名称
     * @param number $id
     */
    public static function get($id=0){
        $array = cache('area_name');
        if (empty($array)) {
            $array = Db::name('area')->column('id,name');
            cache('area_name',$array);
        }
        return $array[$id];
    }
    
}