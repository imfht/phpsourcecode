<?php
namespace app\green\model;
use think\Db;
use think\Model;

class GreenDevice extends Model {

    public function operate(){
        return $this->hasOne('GreenOperate','id','operate_id');
    }

    //用户
    public function user(){
        return $this->hasOne('app\common\model\SystemUser','id','manage_uid');
    }

    //箱满
    public function danger(){
        return $this->hasOne('GreenAlarm','device_id','device_id')->where(['state' => 0]);
    }

    //告警列表
    public function alarm(){
        return $this->hasMany('GreenAlarm','device_id','device_id');
    }

    //查找最近10个回收柜
    public  function selectNear($param){
        return Db::query('select id,longitude,latitude,title,address from '.config('database.prefix').'green_device where latitude > '.$param['latitude'].'-1 and latitude < '.$param['latitude'].'+1 and longitude > '.$param['longitude'].'-1 and longitude < '.$param['longitude'].'+1 order by ACOS(SIN(('.$param['latitude'].' * 3.1415) / 180 ) *SIN((latitude * 3.1415) / 180 ) +COS(('.$param['latitude'].' * 3.1415) / 180 ) * COS((latitude * 3.1415) / 180 ) *COS(('.$param['longitude'].'* 3.1415) / 180 - (longitude * 3.1415) / 180 ) ) * 6380 asc limit 10');
    }
}