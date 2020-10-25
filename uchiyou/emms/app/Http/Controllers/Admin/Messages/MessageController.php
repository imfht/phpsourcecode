<?php
namespace App\Http\Controllers\Admin\Messages;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Message;
use App\Util\MessageUtil;
use App\Util\PaginateUtil;

class MessageController extends Controller{
	
	public function getMyMessageNumber(){
		$user = Auth::user();
		$messagesNumber = Message::where('recipient_id',$user->id)
		->where('status',0)
		->count();
		return $messagesNumber;
	}
	public function delete($id){
		$message = Message::findOrFail($id);
		$user = Auth::user();
		if($message->recipient_id != $user->id){
			abort(403,'您没有权限删除该消息');
		}
		$message->delete();
		return $this->jsonResult(0);
	}
	public function show(){
		$user = Auth::user();
		$messages = Message::where('status', 0)
		->where('recipient_id',$user->id)
		->orderBy('created_at','desc')
		->paginate(PaginateUtil::PAGE_SIZE);
		foreach ($messages as $message){
			// 如果是 物资购买事件，则为之添加相应的事件源的 url.
			/* if($message->action == MessageUtil::EVENT_PURCHASEAPPLY){
				$message->content = $message->content .
					'<div>
                          <a href="/admin/messages/'.$message->id.'/event/material/apply/'.$message->addition.'/approved" class="btn btn-info">同意</a>
                          <a href="/admin/messages/'.$message->id.'/event/material/apply/'.$message->addition.'/rejected" class="btn btn-danger">不同意</a>
                                    </div>';
				continue;
			} */
			// 如果是回复性只读消息，那么阅读该消息后，下次就不再主动提示了
			if($message->action == MessageUtil::EVENT_REPLY || $message->action == MessageUtil::EVENT_READ_ONCE){
				Message::where('id',$message->id)
				->update(['status'=>1]);
				continue;
			}
		}
		return view('message/message',['messages'=>$messages]);
	}
	public function showAll(){
		$user = Auth::user();
		$messages = Message::where('recipient_id',$user->id)
		->orderBy('created_at','desc')
		->paginate(PaginateUtil::PAGE_SIZE);
	/* 	foreach ($messages as $message){
			// 如果是 物资购买事件，则为之添加相应的事件源的 url.
			if($message->action == MessageUtil::EVENT_PURCHASEAPPLY){
				$message->content = $message->content .
					'<div>
                          <a href="/admin/messages/'.$message->id.'/event/material/apply/'.$message->addition.'/approved" class="btn btn-info">同意</a>
                          <a href="/admin/messages/'.$message->id.'/event/material/apply/'.$message->addition.'/rejected" class="btn btn-danger">不同意</a>
                                    </div>';
				continue;
			}
			// 如果是回复性只读消息，那么阅读该消息后，下次就不再主动提示了
			if($message->action == MessageUtil::EVENT_REPLY || $message->action == MessageUtil::EVENT_READ_ONCE){
				Message::where('id',$message->id)
				->update(['status'=>1]);
				continue;
			}
		} */
		return view('message/message',['messages'=>$messages]);
	}
}