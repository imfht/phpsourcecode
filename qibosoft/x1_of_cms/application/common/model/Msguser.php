<?php
namespace app\common\model;

use think\Model;


/**
 * 站内短消息,类似微信首页的用户聊天记录用户列表
 * @package app\admin\model
 */
class Msguser extends Model
{
    //protected $table = '__MEMBERDATA__';
	
	//主键不是ID,要单独指定
	//public $pk = 'id';

    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
    
    /**
     * 把其它人或者是圈子信息加入到会话用户列表
     * @param number $uid 当前用户UID
     * @param number $aid 正数是他人用户UID,负数是圈子ID
     * @return \app\common\model\Msguser
     */
    public static function add($uid=0,$aid=0){
        $map = [
            'uid'=>$uid,
            'aid'=>$aid,
        ];
        $id = self::where($map)->value('id');
        if($id){
            $result = self::where('id',$id)->update(['list'=>time()]);
        }else{
            $map['list'] = time();
            $result = self::create($map);
        }
        return $result;
    }
    
}