<?php
namespace plugins\marketing\model;
use think\Model;


//用户自定义积分
class Money extends Model
{
	
    // 设置当前模型对应的完整数据表名称
    protected $table = '__MONEY__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
	//主键不是ID,要单独指定
	protected $pk = 'id';
	
	/**
	 * 用户自定义积分变化
	 * @param number $uid 用户UID
	 * @param number $money 变化值,可以是负数
	 * @param number $type 自定义积分类型
	 */
	public static function add($uid=0,$money=0,$type=0){
	    $map = [
	            'uid'=>$uid,
	            'type'=>$type
	    ];
	    $info = self::where($map)->find();
	    if (empty($info)){
	        $map['money'] = $money;
	        self::create($map);
	    }else{
	        $data = [
	                'id'=>$info['id'],
	                'money'=>$info['money']+$money,
	        ];
	        self::update($data);
	    }
	}

	
}