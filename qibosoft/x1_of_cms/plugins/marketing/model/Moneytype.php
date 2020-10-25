<?php
namespace plugins\marketing\model;
use think\Model;


//用户自定义积分
class Moneytype extends Model
{
	
    // 设置当前模型对应的完整数据表名称
    protected $table = '__MONEYTYPE__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
	//主键不是ID,要单独指定
	protected $pk = 'id';
	
	
	public static function getList(){
	    return self::order("list desc,id asc")->column(true);
	}
}