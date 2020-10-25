<?php
declare (strict_types = 1);

namespace app\index\model;

use think\Model;

/**
 * @mixin think\Model
 */
class Room extends Base
{
    /*
     * 查询房间数据
     * */
    public function select_room(){
        return self::alias('a')
            ->field('a.*,b.building,c.storey,d.type_name')
            ->join('building b','a.building_id = b.id')
            ->join('storey c','a.storey_id = c.id')
            ->join('layout d','a.type_id = d.id')
            ->paginate(10);
    }

    /*
     * 按条件查询房间数据
     * */
    public function where_room($map){
        return self::alias('a')
            ->field('a.*,b.building,c.storey,d.type_name')
            ->join('building b','a.building_id = b.id')
            ->join('storey c','a.storey_id = c.id')
            ->join('layout d','a.type_id = d.id')
            ->where($map)
            ->paginate(10);
    }

    /*
     * 查询单个房间数据
     * */
    public function find_room($map){
        return self::alias('a')
            ->field('a.*,b.building,c.storey,d.type_name')
            ->join('building b','a.building_id = b.id')
            ->join('storey c','a.storey_id = c.id')
            ->join('layout d','a.type_id = d.id')
            ->where($map)
            ->find();
    }
}
