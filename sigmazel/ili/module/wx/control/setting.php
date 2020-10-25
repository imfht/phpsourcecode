<?php
//版权所有(C) 2014 www.ilinei.com

namespace wx\control;

use admin\model\_log;
use wx\model\_wx_setting;
use ilinei\upload;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/wx/lang.php';

//配置
class setting{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_wx_setting = new _wx_setting();
		
		$wx_setting = $_wx_setting->get();
		
		$wx_setting = format_row_file($wx_setting, 'WX_PEM_CERT');
		$wx_setting = format_row_file($wx_setting, 'WX_PEM_KEY');
		$wx_setting = format_row_file($wx_setting, 'WX_PEM_ROOTCA');
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			$_var['gp_rdoIsOpen'] = $_var['gp_rdoIsOpen'] + 0;
			
			if($_var['gp_rdoIsOpen'] == 1){
				if(empty($_var['gp_txtAccount'])) $_var['msg'] .= $GLOBALS['lang']['wx.setting.validate.account']."<br/>";
				if(empty($_var['gp_txtWxPasswd'])) $_var['msg'] .= $GLOBALS['lang']['wx.setting.validate.passwd']."<br/>";
			}
			
			if(empty($_var['msg'])){
				$_var['gp_rdoType'] = $_var['gp_rdoType'] + 0;
				
				$_wx_setting->set($wx_setting, 'WX_OPEN', $_var['gp_rdoIsOpen']);
				$_wx_setting->set($wx_setting, 'WX_TYPE', $_var['gp_rdoType']);
				$_wx_setting->set($wx_setting, 'WX_ACCOUNT', $_var['gp_txtAccount']);
				$_wx_setting->set($wx_setting, 'WX_PASSWD', $_var['gp_txtWxPasswd']);
				$_wx_setting->set($wx_setting, 'WX_ID', $_var['gp_txtId']);
				$_wx_setting->set($wx_setting, 'WX_TOKEN', $_var['gp_txtToken']);
				$_wx_setting->set($wx_setting, 'WX_APPID', $_var['gp_txtAppId']);
				$_wx_setting->set($wx_setting, 'WX_SECRET', $_var['gp_txtSecret']);
				$_wx_setting->set($wx_setting, 'WX_MENU', $_var['gp_rdoMenu']);
				$_wx_setting->set($wx_setting, 'WX_AUTH', $_var['gp_rdoAuth']);
				$_wx_setting->set($wx_setting, 'WX_JSSDK', $_var['gp_rdoJSSDK']);
				$_wx_setting->set($wx_setting, 'WX_MSGTPL', $_var['gp_rdoMsgTpl']);
				$_wx_setting->set($wx_setting, 'WX_QRCODE', $_var['gp_rdoQRCode']);
				$_wx_setting->set($wx_setting, 'WX_SERVICES', $_var['gp_rdoServices']);
				$_wx_setting->set($wx_setting, 'WX_VOICE', $_var['gp_rdoVoice']);
				$_wx_setting->set($wx_setting, 'WX_PAYMENT', $_var['gp_rdoPayment']);
				$_wx_setting->set($wx_setting, 'WX_PAYSIGNKEY', $_var['gp_txtPaySignKey']);
				$_wx_setting->set($wx_setting, 'WX_PARTNERID', $_var['gp_txtPartnerId']);
				$_wx_setting->set($wx_setting, 'WX_PARTNERKEY', $_var['gp_txtPartnerKey']);
				$_wx_setting->set($wx_setting, 'WX_PEM_CERT', $_var['gp_txtPemCert']);
				$_wx_setting->set($wx_setting, 'WX_PEM_KEY', $_var['gp_txtPemKey']);
				$_wx_setting->set($wx_setting, 'WX_PEM_ROOTCA', $_var['gp_txtPemRootCa']);
				$_wx_setting->set($wx_setting, 'WX_REMIND', $_var['gp_rdoRemind']);
				$_wx_setting->set($wx_setting, 'WX_KEYWROD_CATEGORY', $_var['gp_txtKeywordCategory']);
				
				$_log->insert($GLOBALS['lang']['wx.setting.log.setup'], $GLOBALS['lang']['wx']);

                cache_delete('wx_setting');
				
				show_message($GLOBALS['lang']['wx.setting.message.setup'], "{ADMIN_SCRIPT}/wx/setting");
			}
		}
		
		include_once view('/module/wx/view/setting');
	}
	
	//上传文件
	public function _upload(){
		global $_var;
		
		if(!$_var['current']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.login']);
	
		$file_limit = 2;
		$file_uploaded = $_var['gp_uploaded'] + 0;
		
		if($file_limit > 0 && $file_limit < $file_uploaded + 1) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.limit']."1".$GLOBALS['lang']['admin.validate.swfupload.echo.limit.pic']);
			
		if($_FILES['Filedata']['name']){
            $upload = new upload();

            $upload->init($_FILES['Filedata'], 'doc');
			$upload->save();
			
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.error']);
			if($upload->attach) exit_echo('FILEID:'.$upload->attach['target'].'|'.$upload->attach['name'].'|0|'.$_var['gp_type']);
		}
		
		exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.fail']);
	}
	
}
?>