<?php
//版权所有(C) 2014 www.ilinei.com

namespace wx\control;

use wx\model\_wx;
use wx\model\_wx_msg;
use wx\model\_wx_fans;
use wx\model\_wx_setting;
use ilinei\upload;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/wx/lang.php';

//消息
class msg{
	//默认
	public function index(){
		global $_var;
		
		$_wx = new _wx();
		$_wx_msg = new _wx_msg();
		$_wx_fans = new _wx_fans();
		$_wx_setting = new _wx_setting();
		
		$wx_setting = $_wx_setting->get();
		
		if(!$wx_setting['WX_OPEN']) show_message($GLOBALS['lang']['wx.msg.message.open'], 0);
		
		$search = $_wx_msg->search();
		
		if($_var['gp_do'] == 'delete_list'){
			$days5 = strtotime('-5 days');
			$_wx_msg->delete("CREATETIME <= '{$days5}'");
		}
		
		$count = $_wx_msg->get_count($search['wheresql']);
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$fans_openids = array();
			
			$msg_list = $_wx_msg->get_list($start, $perpage, $search['wheresql']);
			foreach ($msg_list as $key => $msg) {
				if(!in_array($msg['FROMUSERNAME'], $fans_openids)) $fans_openids[] = $msg['FROMUSERNAME'];
			}
			
			if(count($fans_openids) > 0){
				$fans_list = $_wx_fans->get_all("AND OPENID IN(".eimplode($fans_openids).")");
				
				foreach ($fans_list as $key => $fans){
					foreach ($msg_list as $ckey => $msg){
						if($msg['FROMUSERNAME'] == $fans['OPENID']){
							$msg_list[$ckey]['NICKNAME'] = $fans['NICKNAME'];
							break;
						}
					}
				}
			}
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/wx/msg{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/wx/view/msg');
	}
	
	//语音
	public function _voice(){
		global $_var;
		
		$_wx = new _wx();
		$_wx_msg = new _wx_msg();
		$_wx_setting = new _wx_setting();
		
		$wx_setting = $_wx_setting->get();
		
		$mfile = '';
		
		$upload = new upload();
		$tmpdir = $upload->get_target_dir('portal');
		
		$wx_msg = $_wx_msg->get_by_id($_var['gp_msgid'] + 0);
		if(!$wx_msg) exit_json_message('参数错误！');
		
		if(substr($wx_msg['MEDIAID'], 0, 7) == 'portal/') $mfile = $wx_msg['MEDIAID'];
		else{
			$access_token = $_wx->token($wx_setting['WX_APPID'], $wx_setting['WX_SECRET']);
			
			$rtn = $_wx->request("http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$wx_msg[MEDIAID]}");
			$tmpfile = 'portal/'.$tmpdir.get_uuid().'.'.$wx_msg['FORMAT'];
			
			$fp = @fopen(ROOTPATH.'/attachment/'.$tmpfile, 'w+');
			@fwrite($fp, $rtn);
			fclose($fp);
			
			if(is_file(ROOTPATH.'/attachment/'.$tmpfile)){
				$_wx_msg->update($wx_msg['WX_MSGID'], array('MEDIAID' => $tmpfile));
				$mfile = $tmpfile;
			}
		}
		
		if(empty($mfile)) exit_json_message('下载媒体文件出错！');
		
		exit_json_message("attachment/{$mfile}", true);
	}
	
}
?>