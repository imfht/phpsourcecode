<?php
namespace Common\Model;
use Think\Model;

class CityModel extends Model{

    public function getSelect(){
        $city_name=F('CityName');
        if(!$city_name){
            $city_name=$this->where('status=1')->getField('id,title');
            F('CityName',$city_name);
        }
        return $city_name;
    }


}
?>