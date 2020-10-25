<?php 
/**
 * 处理微信的普通消息
 * 对于无法在五秒内处理并回复的微信服务器请求 或者 这里尚未实现的响应操作, 直接回复空串, 微信服务器不会对此作任何处理, 并且不会发起重试。
 */
namespace Weixin\Event;
use Weixin\Event\BaseEvent;

class OrdinaryEvent extends BaseEvent
{
	protected function _initialize()
	{
		/* 由于微信的一般消息重试机制, 15秒内, 通过msgid消息去重 */
		$msgID = getMsgField('msgid', 'int', $msg); //取得消息id
		// coding ...

	}

	/* 暂不实现，简单处理, 直接回显空串 */
	public function link($msg)
	{
		//
	}

	/* 暂不实现，简单处理, 直接回显空串 */
	public function text()
	{
		echo '';
	}

	/* 暂不实现，简单处理, 直接回显空串 */
	public function image()
	{
		echo '';
	}

	/* 暂不实现，简单处理, 直接回显空串 */
	public function voice()
	{
		echo '';
	}

	/* 暂不实现，简单处理, 直接回显空串 */
	public function video()
	{
		echo '';
	}

	/* 暂不实现，简单处理, 直接回显空串 */
	public function location()
	{
		echo '';
	}

}
?>