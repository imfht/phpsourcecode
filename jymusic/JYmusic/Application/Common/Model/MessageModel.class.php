<?php
namespace Common\Model;

use Think\Model;

/**
 * 消息通知模型
 */
class MessageModel extends Model{
	
	 /**获取全部没有读取过的消息
     * @param $uid 用户ID
     */
    public function getUnreadMsg($uid){
    	$map['to_uid'] = $uid;
    	$map['is_read'] = 0;
        $msg = D('message')->where($map)->order('id desc')->field('is_read,to_uid,is_tip',true)->limit(9999)->select();
        return $msg;
    }    
    
    	
	 /**获取全部没有提示过的消息
	 * @param $uid 用户ID
	 */
    public function getNoTip($uid){
    	$map['to_uid'] = $uid;
    	$map['is_tip'] = 0;
        $msg = D('message')->where($map)->order('id desc')->field('is_read,to_uid,is_tip',true)->limit(9999)->select();
        return $msg;
    }   
    
    /**设置全部未提醒过的消息为已读
     * @param $uid
    */
    public function setAllRead($uid){
        D('message')->where('to_uid=' . $uid . ' and  is_read=0')->setField('is_read', 1);
    }
    
    
    /**发送信息
    * @param    $type 消息类型 system系统，letter私信，app应用
     * @param $uid
    */
    public function sendMsg($to_uid,$title = '您有新的消息',$content,$type='system',$post_uid=0,$reply_id=0){
		$data['post_uid'] = $post_uid;
		if ($post_uid){
			$data['post_uname'] = get_nickname($post_uid);
		}else{
			$data['post_uname'] = '系统提醒';
		}
		$data['to_uid'] = $to_uid;
		$data['title'] = $title;
		$data['content'] = $content;
		$data['reply_id'] = $reply_id;  		
		$data['post_time'] = NOW_TIME; 
		$data['type'] = $type;
    	$id = $this->add($data);   
    	return $id;
    }
}
