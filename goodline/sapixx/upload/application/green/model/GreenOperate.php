<?php
namespace app\green\model;
use think\Db;
use think\Model;

class GreenOperate extends Model {

    //用户
    public function user(){
        return $this->hasOne('app\common\model\SystemUser','id','uid');
    }

    //查找最近运营商
    public  function selectAll($param){
       return $info = Db::query('select * from '.config('database.prefix').'green_operate where latitude > '.$param['latitude'].'-1 and latitude < '.$param['latitude'].'+1 and longitude > '.$param['longitude'].'-1 and longitude < '.$param['longitude'].'+1 order by ACOS(SIN(('.$param['latitude'].' * 3.1415) / 180 ) *SIN((latitude * 3.1415) / 180 ) +COS(('.$param['latitude'].' * 3.1415) / 180 ) * COS((latitude * 3.1415) / 180 ) *COS(('.$param['longitude'].'* 3.1415) / 180 - (longitude * 3.1415) / 180 ) ) * 6380 asc limit 1');
    }
}