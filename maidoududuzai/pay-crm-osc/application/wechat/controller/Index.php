<?php

namespace app\wechat\controller;

use \think\Db;
use \app\common\WeChat;
use \app\common\WeChatMerchant;

class Index
{

	public function __construct()
	{
		
	}

	public function log($content = '')
	{

		Tool::log($content, 'WeChat');

	}

	public function index()
	{

		$where = [];
		$where['m.merchant_no'] = input('param.merchant_no');
		$this->merchant = Db::name('merchant')
			->alias('m')
			->join('merchant_weixin mw', 'm.merchant_id = mw.merchant_id', 'LEFT')
			->where($where)
			->field('m.merchant_id, m.merchant_NO, mw.appid, mw.appsecret, mw.token, mw.encodingaeskey')
			->find();

		if($this->merchant) {
			// mch
			return null;
		} else {
			// sys
			return (new Home())->index();
		}

		$this->MchWeChat = WeChatMerchant::init($this->merchant);

		try {
			$this->Receive = $this->MchWeChat->load('Receive');
		} catch (\Exception $e) {
			echo $e->getMessage();
			exit();
		}

		$this->data = $this->Receive->getReceive();

		$this->log($this->data);

		$this->openid = $this->Receive->getOpenid();

		$this->MchUser = model('MchUser')->get_one(['openid' => $this->openid, 'merchant_id' => $this->merchant['merchant_id']]);
		if(!$this->MchUser) {
			model('MchUser')->allowField(true)->save(['openid' => $this->openid, 'merchant_id' => $this->merchant['merchant_id']]);
		}

		switch($this->Receive->getMsgType()) {
			case 'text':
				$content = $this->Receive->getReceive('Content');
				return $this->_keys($content);
			break;
			case 'event':
				$event = $this->Receive->getReceive('Event');
				return $this->_event(strtolower($event));
			break;
			case 'image':
				return $this->_image();
			break;
		}

	}

	public function _keys($content)
	{
		return $this->Receive->text($content)->reply();
	}

	public function _event($event)
	{
		switch($event) {
			// 粉丝关注事件
			case 'subscribe':
				$UserInfo = $this->MchWeChat->getUserInfo($this->openid);
				return $this->Receive->text('欢迎关注公众号！')->reply();
			break;
			// 粉丝取消关注
			case 'unsubscribe':
				$fields = [
					'subscribe' => 0,
					'subscribe_time' => 0,
				];
				model('MchUser')->allowField(true)->save($fields, ['openid' => $this->openid, 'merchant_id' => $this->merchant['merchant_id']]);
			break;
			// 点击微信菜单
			case 'click':
				return $this->Receive->text('你点了菜单！')->reply();
			// 卡券审核通过
			break;
			case 'card_pass_check':
				
			break;
			// 卡券审核不通过
			case 'card_not_pass_check':
				
			break;
			// 领取卡券
			case 'user_get_card':
				$UserCardInfo = $this->MchWeChat->getUserCardInfo($this->data['CardId'], $this->data['UserCardCode']);
			break;
			// 删除卡券
			case 'user_del_card':
				
			break;
			// 查看卡券
			case 'user_view_card':
				$UserCardInfo = $this->MchWeChat->getUserCardInfo($this->data['CardId'], $this->data['UserCardCode']);
			break;
			// 激活会员卡
			case 'submit_membercard_user_info':
				$UserCardInfo = $this->MchWeChat->getUserCardInfo($this->data['CardId'], $this->data['UserCardCode']);
			// 其他事件默认操作
			default:
				/**/
			break;
		}
	}

	public function _image()
	{
		return $this->Receive->text('图片')->reply();
	}

	public function _default()
	{
		return $this->Receive->text('未定义')->reply();
	}

}

