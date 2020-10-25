<?php
namespace App\Http\Controllers\Admin\Messages;

use Illuminate\Http\Request;
use App\ApplyRecord;
use App\Message;
use Illuminate\Support\Facades\Auth;
use App\Util\MessageUtil;
/**
 * 此类已经废弃
 *  通知消息引起用户点击的事件处理类
 * @author Administrator
 *
 */
class EventHandlerController{
	
	/*
	 * 申请购买物资审批消息引发的事件处理方法
	 */
	public function handleMaterialPurchaseApply(Request $request,$messageId,$applyRecordId,$operate){
		$status = '0';
		if(strcmp($operate, 'approved') == 0){
			$status = '1';
		}elseif (strcmp($operate, 'rejected') == 0){
			$status = '2';
		}
		// 修改申请购买物资记录和消息记录的状态
		$recordResult = ApplyRecord::where('id',$applyRecordId)
		->update(['statuses'=> $status]);
		$messageResult = Message::where('id',$messageId)
		->update(['status'=>1]);
		// 为申请者生成一条消息通知，
		$message = Message::where('id',$messageId)->first();
		if(empty($message) == false){
			$user = Auth::user();
			Message::saveMessage($message->recipient_id, $message->sender_id,
					MessageUtil::getPurchaseApplyReply($message->content, $user->name, $operate),
					MessageUtil::EVENT_REPLY, '0');
		}
		
		return $recordResult && $messageResult ? '1' : '0';
	}
}