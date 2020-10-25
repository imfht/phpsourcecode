<?php
namespace app\common\model;

use think\Model;

/**
 * 钩子例表
 * @package app\admin\model
 */
class Hook extends Model
{
    protected $table = '__HOOK__';
	
	//主键不是ID,要单独指定
	//public $pk = 'id';

    // 自动写入时间戳
    //protected $autoWriteTimestamp = true;
    
    public static function getTitleList($map = [])
    {
        return self::where($map)->order('list desc,id desc')->column('id,name');
    }
    
    public static function getTitleListByKey($map = [])
    {
        $array = self::where($map)->order('list desc,id desc')->column('name,about');
        foreach ($array AS $name=>$title){
            $array[$name] = $name." ({$title})";
        }        
        return $array;
    }



	
}