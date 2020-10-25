<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/5/3
 * Time: 14:06
 */
namespace app\first\model;

use think\Model;

class Carousel extends Model{
    protected $pk = 'id';
    protected $auto = ['date','dates'];
    protected static $_field = ['id','title','keywords','url','image','url'];

    public static function getCarousel($data=[]){
        return is_array($data)?self::field(self::$_field)
            ->where($data)->find():self::field(self::$_field)->find($data);
    }

    protected function setDatesAttr(){
        return time();
    }

    protected function setDateAttr(){
        return time();
    }

    public function getImageAttr($value){
        if(empty($value)){
            return null;
        }else if(strpos($value,'://')===false){
            return request()->Domain().DIRECTORY_SEPARATOR.substr($value,1);
        }else{
            return $value;
        }
    }
}