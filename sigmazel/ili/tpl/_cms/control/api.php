<?php
//版权所有(C) 2014 www.ilinei.com

namespace tpl\_cms\control;

use wx\model\_wx;
use wx\model\_wx_menu;
use wx\model\_wx_fans;
use wx\model\_wx_setting;
use wx\model\_wx_msg;

use cms\model\_article;

//微信接口
class api{
	//默认
	public function index(){
		global $_var, $db, $setting;
		
		if($_GET['echostr']) exit_echo($_GET['echostr']);
		
		$db->connect();
		
		$_wx = new _wx();
		$_wx_menu = new _wx_menu();
		$_wx_fans = new _wx_fans();
		$_wx_setting = new _wx_setting();
		$_wx_msg = new _wx_msg();
		
		$_article = new _article();
		
		$wx_setting = $_wx_setting->get();
		
		$wx_setting = format_row_file($wx_setting, 'AUTOPIC');
		$wx_setting = format_row_file($wx_setting, 'SUBSCRIBEPIC');
		
		//如果未设置关键词分类标识号，默认为B3
		empty($wx_setting['WX_KEYWROD_CATEGORY']) && $wx_setting['WX_KEYWROD_CATEGORY'] = 'B3';
		
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		$postObj = (array) $postObj;
		
		//如果开启客服消息，直接回复
		if ($postObj['MsgType'] != 'event'){
			if ($wx_setting['WX_SERVICES'] + 0 > 0) $_wx->response_service($postObj);
			
			//添加微信消息记录
			$_wx_msg->insert($postObj);
		}
		
		//文字消息
		if($postObj['MsgType'] == 'text' && $postObj['Content']){
			$articles = $_article->get_list($wx_setting['WX_KEYWROD_CATEGORY'], 0, 9, "AND a.KEYWORDS LIKE '%{$postObj[Content]}%'");
			
			//如果未设置关键词，自动回复
			if(count($articles) == 0) $_wx->response_auto($postObj);
			
			//如果单篇关键词且为文本，自动文本
			if(count($articles) == 1 && $articles[0]['TYPE'] == 1){
				$article = $_article->get_by_id($articles[0]['ARTICLEID']);
				$_wx->response_message($postObj, $article['CONTENT']);
			}
			
			$_wx->response_articles($articles, $postObj);
		}
		
		//事件消息
		if($postObj['MsgType'] == 'event'){
			//关注事件
			if($postObj['Event'] == 'subscribe'){
				$wx_fans = $_wx_fans->get_by_openid($postObj['FromUserName']);
				if($wx_fans) $_wx_fans->update($wx_fans['WX_FANSID'], array('SUBSCRIBE' => 1));
				
				//文本回复消息
				if($wx_setting['SUBSCRIBETYPE'] == 1) $_wx->response_message($postObj, $wx_setting['SUBSCRIBETEXT']);
				
				//图文回复消息
				if($wx_setting['SUBSCRIBETYPE'] == 2){
					$news = array(
					'ToUserName' => $postObj['FromUserName'],
					'FromUserName' => $postObj['ToUserName'],
					'CreateTime' => time(), 
					'Items' => array(
						array(
						'Title' => $wx_setting['SUBSCRIBETITLE'], 
						'Description' => $wx_setting['SUBSCRIBEDESCRIPTION'], 
						'PicUrl' => $setting['SiteHost'].$wx_setting['SUBSCRIBEPIC'][3], 
						'Url' => strexists($wx_setting['SUBSCRIBEURL'], 'http://') ? $wx_setting['SUBSCRIBEURL'] : $setting['SiteHost'].$wx_setting['SUBSCRIBEURL']
						)
					));
					
					exit_echo($_wx->response2news($news));
				}
			}
			
			//取消关注事件
			if($postObj['Event'] == 'unsubscribe'){
				$wx_fans = $_wx_fans->get_by_openid($postObj['FromUserName']);
				if($wx_fans) $_wx_fans->update($wx_fans['WX_FANSID'], array('SUBSCRIBE' => 0));
			}
			
			//菜单点击事件
			if($postObj['Event'] == 'CLICK'){
				$wx_menu = $_wx_menu->get_by_key($postObj['EventKey']);
				
				if($wx_menu){
					if($wx_menu['REMARK']) $_wx->response_message($postObj, $wx_menu['REMARK']);
					else{
						$articles = $_article->get_list($wx_setting['WX_KEYWROD_CATEGORY'], 0, 9, "AND a.KEYWORDS LIKE '%{$wx_menu[NAME]}%'");
						
						if(count($articles) == 0) $_wx->response_auto($postObj);
						else $_wx->response_articles($articles, $postObj);
					}
				}
			}
			
			//扫描二维码
			if($postObj['Event'] == 'SCAN'){
				$wx_fans = $_wx_fans->get_by_openid($postObj['FromUserName']);
				if($wx_fans) $_wx_fans->update($wx_fans['WX_FANSID'], array('SUBSCRIBE' => 1));
			}
		}
		
		//回复默认消息
		$_wx->response_message($postObj, $wx_setting['RESPONSETEXT']);
	}
}
?>