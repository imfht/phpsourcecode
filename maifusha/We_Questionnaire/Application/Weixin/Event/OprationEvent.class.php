<?php 
/**
 * 处理微信的操作事件消息
 * 对于无法在五秒内处理并回复的微信服务器请求 或者 这里尚未实现的响应操作, 直接回复空串, 微信服务器不会对此作任何处理, 并且不会发起重试。
 */
namespace Weixin\Event;
use Weixin\Event\BaseEvent;

class OprationEvent extends BaseEvent
{
	protected function _initialize()
	{
		/* 由于微信的事件消息重试机制, 15秒内, 通过 "FromUserName + CreateTime" 消息去重 */
		$msgID = getMsgField('msgid', 'int', $msg); //取得消息id
		// coding ...

	}

	/* 关注事件 */
	public function subscribe()
	{
		echo '';
	}

	/* 取消关注事件 */
	public function unsubscribe()
	{
		echo '';
	}

	/* 暂不实现，简单处理, 直接回显空串 */
	public function scan()
	{
		echo '';
	}

	/* 暂不实现，简单处理, 直接回显空串 */
	public function location()
	{
		echo '';
	}

	/* 暂不实现，简单处理, 直接回显空串 */
	public function click()
	{
		echo '';
	}

	/* 暂不实现，简单处理, 直接回显空串 */
	public function view()
	{
		echo '';
	}

}
?>