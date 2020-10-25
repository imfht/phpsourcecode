<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
	/**
	 * 与模型关联的数据表
	 *
	 * @var string
	 */
	protected $table = 'mm_messages';
	public static function saveMessage($senderId,$recipientId,$content,$action,$addition){
		$message =new Message();
		$message->sender_id = $senderId;
		$message->recipient_id = $recipientId;
		$message->content = $content;
		$message->type = 1;// 表示通知消息
		$message->status = 0;// 表示未读
		$message->action = $action; // 消息接受者可以操作的事件.见 MessageUtil 
		$message->addition = $addition;
		return $message->save();
	}
}
