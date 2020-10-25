<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\control;

use admin\model\_log;
use admin\model\_setting;
use ilinei\upload;
use ilinei\image;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/admin/lang.php';

//配置
class setting{
	//参数
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_setting = new _setting();
		
		$setting_array = $_setting->get();
		
		if($setting_array['SiteLogo']) $setting_array = format_row_file($setting_array, 'SiteLogo');
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtProductName']))$_var['msg'] .= $GLOBALS['lang']['admin.setting_site.validate.product']."<br/>";
			if(empty($_var['gp_txtCopyright']))$_var['msg'] .= $GLOBALS['lang']['admin.setting_site.validate.copyright']."<br/>";
			
			if(empty($_var['gp_txtSiteHost']))$_var['msg'] .= $GLOBALS['lang']['admin.setting_site.validate.host']."<br/>";
			if(empty($_var['gp_txtSiteName']))$_var['msg'] .= $GLOBALS['lang']['admin.setting_site.validate.name']."<br/>";
			if($_var['gp_rdoSiteIsShowIcp'] == 1 && empty($_var['gp_txtSiteIcp'])) $_var['msg']  .= $GLOBALS['lang']['admin.setting_site.validate.icp']."<br/>";
			if($_var['gp_rdoSiteIsClosed'] == 1 && empty($_var['gp_txtSiteClosedReason'])) $_var['msg'] .= $GLOBALS['lang']['admin.setting_site.validate.close']."<br/>";
			if(empty($_var['gp_txtSiteIndex']))$_var['msg'] .= $GLOBALS['lang']['admin.setting_site.validate.index']."<br/>";
			if(empty($_var['gp_txtSiteTheme']))$_var['msg'] .= $GLOBALS['lang']['admin.setting_site.validate.theme']."<br/>";

			if(empty($_var['msg'])){
				$_var['gp_txtImageWidth'] = $_var['gp_txtImageWidth'] + 0;
				$_var['gp_txtImageHeight'] = $_var['gp_txtImageHeight'] + 0;
				
				$_var['gp_txtNewDays'] = $_var['gp_txtNewDays'] + 0;
				
				$_var['gp_txtWarnLimit'] = $_var['gp_txtWarnLimit'] + 0;
				$_var['gp_txtSmtpPort'] = $_var['gp_txtSmtpPort'] + 0;
				
				$_setting->set('Language', $_var['gp_sltLanguage']);
				$_setting->set('ProductName', $_var['gp_txtProductName']);
				$_setting->set('SiteName', $_var['gp_txtSiteName']);
				$_setting->set('SiteHost', $_var['gp_txtSiteHost']);
				$_setting->set('CompanyName', $_var['gp_txtCompanyName']);
				$_setting->set('SitePhone', $_var['gp_txtSitePhone']);
				$_setting->set('SiteEmail', $_var['gp_txtSiteEmail']);
				$_setting->set('SiteAddress', $_var['gp_txtSiteAddress']);
				$_setting->set('SiteIsClosed', $_var['gp_rdoSiteIsClosed']);
				$_setting->set('SiteClosedReason', $_var['gp_txtSiteClosedReason']);
				$_setting->set('SiteIsShowIcp', $_var['gp_rdoSiteIsShowIcp']);
				$_setting->set('SiteIcp', $_var['gp_txtSiteIcp']);
				$_setting->set('SiteHTAccess', $_var['gp_rdoSiteHTAccess']);
				$_setting->set('SiteLogo', $_var['gp_hdnSiteLogo']);
				
				$_setting->set('Copyright', $_var['gp_txtCopyright']);
				$_setting->set('CopyrightText', $_var['gp_txtCopyrightText']);
				
				$_setting->set('ImageWidth', $_var['gp_txtImageWidth']);
				$_setting->set('ImageHeight', $_var['gp_txtImageHeight']);
				
				$_setting->set('ThumbWidth', $_var['gp_txtThumbWidth']);
				$_setting->set('ThumbHeight', $_var['gp_txtThumbHeight']);
				
				$_setting->set('NewDays', $_var['gp_txtNewDays']);
				
				$_setting->set('SiteTheme', $_var['gp_txtSiteTheme']);
				$_setting->set('SiteIndex', $_var['gp_txtSiteIndex']);
				$_setting->set('MobileEnabled', $_var['gp_cbxMobileEnabled']);
				$_setting->set('MobileIndex', $_var['gp_txtMobileIndex']);
				
				$_setting->set('ThirdSms', $_var['gp_cbxThirdSms']);
				$_setting->set('SmsKey', $_var['gp_txtSmsKey']);
				$_setting->set('SmsSecret', $_var['gp_txtSmsSecret']);
				$_setting->set('SmsPassword', $_var['gp_txtSmsPassword']);
				$_setting->set('SmsSuffix', $_var['gp_txtSmsSuffix']);
				
				$_setting->set('ThirdSMTP', $_var['gp_cbxThirdSMTP']);
				$_setting->set('SmtpHost', $_var['gp_txtSmtpHost']);
				$_setting->set('SmtpPort', $_var['gp_txtSmtpPort']);
				$_setting->set('SmtpUser', $_var['gp_txtSmtpUser']);
				$_setting->set('SmtpPassword', $_var['gp_txtSmtpPassword']);
				
				$_setting->set('ThirdWeixin', $_var['gp_cbxThirdWeixin']);
				$_setting->set('ThirdWeixinAppID', $_var['gp_txtThirdWeixinAppID']);
				$_setting->set('ThirdWeixinAppSecret', $_var['gp_txtThirdWeixinAppSecret']);
				
				$_setting->set('ThirdQQ', $_var['gp_cbxThirdQQ']);
				$_setting->set('ThirdQQAppID', $_var['gp_txtThirdQQAppID']);
				$_setting->set('ThirdQQAppKey', $_var['gp_txtThirdQQAppKey']);
				
				$_setting->set('ThirdWeibo', $_var['gp_cbxThirdWeibo']);
				$_setting->set('ThirdWeiboAppKey', $_var['gp_txtThirdWeiboAppKey']);
				$_setting->set('ThirdWeiboAppSecret', $_var['gp_txtThirdWeiboAppSecret']);
				
				$_setting->set('SiteTemplateCache', $_var['gp_cbxSiteTemplateCache']);
				$_setting->set('SiteLogDatabase', $_var['gp_cbxSiteLogDatabase']);
				
				$_setting->set('ViewLog', $_var['gp_cbxViewLog']);
				$_setting->set('CheckUpdating', $_var['gp_cbxCheckUpdating']);
				
				$_setting->set('InviteRedirect', $_var['gp_txtInviteRedirect']);
				$_setting->set('WarnLimit', $_var['gp_txtWarnLimit']);
				
				if($_var['current']['USERID'] == -1){
					$_setting->set('HelpAdminFile', $_var['gp_txtHelpAdminFile']);
				}
				
				$_log->insert($GLOBALS['lang']['admin.setting_site.log.setup'], $GLOBALS['lang']['admin.setting']);

                cache_delete('setting');
				
				show_message($GLOBALS['lang']['admin.setting_site.message.setup'], "{ADMIN_SCRIPT}/admin/setting");
			}
		}
		
		include_once view('/module/admin/view/setting_site');
	}
	
	//SEO
	public function seo(){
		global $_var;
		
		$_log = new _log();
		$_setting = new _setting();
		
		$setting_array = $_setting->get();
		
		if($_var['gp_formsubmit']){
			$_setting->set('HotWords', $_var['gp_txtHotWords']);
			$_setting->set('SeoTitle', $_var['gp_txtSeoTitle']);
			$_setting->set('SeoTags', $_var['gp_txtSeoTags']);
			$_setting->set('SeoMetaKeywords', $_var['gp_txtSeoMetaKeywords']);
			$_setting->set('SeoMetaDescription', $_var['gp_txtSeoMetaDescription']);
			$_setting->set('SeoHead', $_var['gp_txtSeoHead']);
			$_setting->set('SeoFoot', $_var['gp_txtSeoFoot']);
			$_setting->set('SeoMobileHead', $_var['gp_txtSeoMobileHead']);
			$_setting->set('SeoMobileFoot', $_var['gp_txtSeoMobileFoot']);
			$_setting->set('NOIP', $_var['gp_txtNoIP']);
			$_setting->set('ALLOWIP', $_var['gp_txtAllowIP']);
			
			$_log->insert($GLOBALS['lang']['admin.setting_seo.log.setup'], $GLOBALS['lang']['admin.setting']);

            cache_delete('setting');
			
			show_message($GLOBALS['lang']['admin.setting_seo.message.setup'], "{ADMIN_SCRIPT}/admin/setting/seo");
		}
		
		include_once view('/module/admin/view/setting_seo');
	}
	
	//文件上传
	public function _upload(){
		global $_var;
		
		if(!$_var['current']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.login']);
		
		if($_FILES['Filedata']['name']){
			$upload = new upload();
			$upload->init($_FILES['Filedata'], 'portal');
			
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.error']);
			if(!$upload->attach['isimage']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.image']);
			
			$upload->save();
			
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.move']);
			
			if($upload->attach) {
				$temp_img_size = getimagesize('attachment/'.$upload->attach['target']);
				
				exit_echo('FILEID:'.$upload->attach['target'].'|'.$upload->attach['name'].'|0|'.$temp_img_size[0].'|'.$temp_img_size[1]);
			}
		}
		
		exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.fail']);
	}
	
	//清除缓存
	public function _clear(){
		$files = scandir('./_cache/');
		
		foreach ($files as $file){
			if(!is_dir('./_cache/'.$file) && substr($file, 0, 5) == 'cache'){
				unlink(ROOTPATH.'/_cache/'.$file);
			}
		}
		
		exit_json_message($GLOBALS['lang']['admin.setting_site.message.clear']);
	}
	
}
?>