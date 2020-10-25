<?php
namespace app\common\model;

use think\Model;

/**
 * 后台配置模型
 * @package app\admin\model
 */
class Module extends Model
{
    // 设置当前模型对应的完整数据表名称memberdata
    //protected $table = '__MEMBERDATA__';
	
	//主键不是ID,要单独指定
	//public $pk = 'id';

    // 自动写入时间戳
    protected $autoWriteTimestamp = false;


	public static function getById($id = '')
    {
        return self::get(['id' => $id]);
    }
    public static function getNameById($id = '')
    {
        return self::where(['id' => $id])->value('name');
    }
    
    public static function getTitleList($map = [])
    {
        return self::where($map)->order('list desc,id desc')->column('id,name');
    }
    
    public static function getList($map = [])
    {
        $array = self::where($map)->order('list desc,id desc')->select();
        return getArray($array);
    }
}