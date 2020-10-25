<?php

namespace app\common\model;
use think\Model;


/**
 * 好友粉丝
 *
 */
class Friend extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__FRIEND__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
	//主键不是ID,要单独指定
	protected $pk = 'id';
	
	/**
	 * 加粉丝
	 * @param number $uid 当前登录用户
	 * @param number $suid 其它用户
	 */
	public static function add($uid=0,$suid=0){
	    $map = [
	        'uid'=>$uid,
	        'suid'=>$suid,
	    ];
	    $result = self::where($map)->update(['update_time'=>time()]);
	    if (empty($result)) {
	        self::create($map);
	    }
	}
	
	/**
	 * 标签获取数据
	 * @param array $config
	 */
	public static function get_label($config=[]){
	    $cfg = unserialize($config['cfg']);
	    $uid = $cfg['uid'];
	    $suid = $cfg['suid'];
	    $rows = $cfg['rows'];
	    $rows>0 || $rows=10;
	    $map = [];
	    $uid && $map['uid'] = $uid;
	    $suid && $map['suid'] = $suid;
	    if($cfg['where']){  //用户自定义的查询语句
	        $_array = fun('label@where',$cfg['where'],$cfg);
	        if($_array){
	            $map = array_merge($map,$_array);
	        }
	    }
	    $array = self::where($map)->order('update_time desc,id desc')->paginate($rows);
	    $array = getArray($array);
 	    foreach($array['data'] AS $key=>$rs){
 	        if($suid){
 	            $rs['he_id'] = $rs['uid'];
 	        }else{
 	            $rs['he_id'] = $rs['suid'];
 	        }
 	        $array['data'][$key] = $rs;
 	    }
// 	    if ($suid) {
// 	        $listdb = [];
// 	        foreach($array['data'] AS $rs){
// 	            $listdb[$rs['type']][] = $rs;
// 	        }
// 	        $array['data'] = $listdb;
// 	    }	    
	    return $array;
	}
}