<?php
//版权所有(C) 2014 www.ilinei.com

namespace wx\control;

use wx\model\_wx;
use wx\model\_wx_fans;
use wx\model\_wx_setting;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/wx/lang.php';

//粉丝
class fans{
	//默认
	public function index(){
		global $_var;
		
		$_wx = new _wx();
		$_wx_fans = new _wx_fans();
		$_wx_setting = new _wx_setting();
		
		$wx_setting = $_wx_setting->get();
		
		if(!$wx_setting['WX_OPEN']) show_message($GLOBALS['lang']['wx.fans.message.open'], 0);
		elseif(!$wx_setting['WX_TYPE']) show_message($GLOBALS['lang']['wx.fans.message.type'], 0);
		
		$search = $_wx_fans->search();
		
		$count = $_wx_fans->get_count("{$search[wheresql]}");
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
		
			$fans_list = $_wx_fans->get_list($start, $perpage, "{$search[wheresql]}");
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/wx/fans{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/wx/view/fans');
	}
	
	//清空数据
	public function _data(){
		$_wx_fans = new _wx_fans();
		$_wx_fans->delete("WXID = 0");
	
		include_once view('/module/wx/view/fans_data');
	}
	
	//获取公众号粉丝
	public function _fetch(){
		global $_var, $ADMIN_SCRIPT;
		
		$_wx = new _wx();
		$_wx_fans = new _wx_fans();
		$_wx_setting = new _wx_setting();
		
		$wx_setting = $_wx_setting->get();
		
		$access_token = $_wx->token($wx_setting['WX_APPID'], $wx_setting['WX_SECRET']);
		$result = $_wx->request("https://api.weixin.qq.com/cgi-bin/user/get?access_token={$access_token}".($_var['gp_next_openid'] ? "&next_openid={$_var[gp_next_openid]}" : ''));
		$result = json_decode($result, 1);
		
		if(count($result['data']['openid']) == 0){
			header("location:{$ADMIN_SCRIPT}/wx/fans");
			exit(0);
		}else{
			foreach($result['data']['openid'] as $key => $val){
				$tmp_fans = $_wx->request("https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$val}&lang=zh_CN", '', 'GET');
				$tmp_fans = json_decode($tmp_fans, 1);
				
				$wx_fans = $_wx_fans->get_by_openid($tmp_fans['openid']);
				
				if($wx_fans){
					$_wx_fans->update($wx_fans['WX_FANSID'], array(
					'SUBSCRIBE' => $tmp_fans['subscribe'],
					'OPENID' => $tmp_fans['openid'],
					'NICKNAME' => $tmp_fans['nickname'],
					'SEX' => $tmp_fans['sex'],
					'CITY' => $tmp_fans['city'],
					'COUNTRY' => $tmp_fans['country'],
					'PROVINCE' => $tmp_fans['province'],
					'LANGUAGE' => $tmp_fans['language'],
					'HEADIMGURL' => $tmp_fans['headimgurl'],
					'SUBSCRIBE_TIME' => $tmp_fans['subscribe_time']
					));
				}else{
					$_wx_fans->insert(array(
					'WXID' => 0, 
					'SUBSCRIBE' => $tmp_fans['subscribe'],
					'OPENID' => $tmp_fans['openid'],
					'NICKNAME' => $tmp_fans['nickname'],
					'SEX' => $tmp_fans['sex'],
					'CITY' => $tmp_fans['city'],
					'COUNTRY' => $tmp_fans['country'],
					'PROVINCE' => $tmp_fans['province'],
					'LANGUAGE' => $tmp_fans['language'],
					'HEADIMGURL' => $tmp_fans['headimgurl'],
					'SUBSCRIBE_TIME' => $tmp_fans['subscribe_time']
					));
				}
				
				unset($tmp_fans);
				unset($wx_fans);
			}
		}
		
		include_once view('/module/wx/view/fans_data');
	}
	
}
?>