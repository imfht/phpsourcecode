<?php
namespace Home\Widget;
use Think\Controller;

/**
* 微信事件处理
*/
class EventWidget extends Controller
{
	
	public function index($data)
	{
		$wx = new \Common\Lib\Weixin\Weixin();
		if ( $data['Event']['Event'] == 'subscribe' ) {
			$result = array(
                'MsgType' => 'text',
                'Content' => '感谢您的关注！所有功能正在开发中，请您耐心等待！'
            );
		}
		
		return $wx->toWeixin($result);
	}

	public function cxkj($data)
	{
		$wx = new \Common\Lib\Weixin\Weixin();
		if ( $data['Event']['Event'] == 'subscribe' ) {
			$result = array(
                'MsgType' => 'text',
                'Content' => '感谢您的关注！所有功能正在开发中，请您耐心等待！'
            );
		}
		if ( $data['Event']['Event'] == 'CLICK' ) {
			if ( $data['Event']['EventKey'] == 'COMP_PROJ' ) {
				$result = array(
	                'MsgType' => 'news',
	                'Content' => array(
	                	array(
	                		'香港环境保护协会网络中心建设',
	                		'',
	                		'http://wx.yanda.net.cn/uploads/cxkj/image/hkepa.png',
	                		'http://hkepa.ysder.com'
	                	),
	                	array(
	                		'唯尔易购B2C商城',
	                		'',
	                		'http://wx.yanda.net.cn/uploads/cxkj/image/hkepa200.png',
	                		'http://www.wellego.com'
	                	),
	                ),
            	);
			}
		}
		if ( $data['Event']['Event'] == 'scancode_push' ) {
			if ( $data['Event']['EventKey'] == 'rselfmenu_0_1' ) {
				$result = array(
	                'MsgType' => 'text',
	                'Content' => '您扫描了二维码。二维码的信息为：'.$data['Event']['ScanResult ']
            	);
			}
		}
		
		return $wx->toWeixin($result);
	}

	public function hkepa($data)
	{
		$wx = new \Common\Lib\Weixin\Weixin();
		if ( $data['Event']['Event'] == 'subscribe' ) {
			$result = array(
                'MsgType' => 'text',
                'Content' => '感谢您关注【香港环境保护协会】。'
            );
		}
		if ( $data['Event']['Event'] == 'CLICK' ) {
			if ( $data['Event']['EventKey'] == 'V1001_TODAY_MUSIC' ) {
				$result = array(
	                'MsgType' => 'text',
	                'Content' => '点击功能正在开发中，请您耐心等待！'
            	);
			}
		}
	}
}