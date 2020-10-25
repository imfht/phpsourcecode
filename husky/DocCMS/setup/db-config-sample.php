<?php
//数据库配置字段
define('DB_HOSTNAME','localhost');
define('DB_USER','user');
define('DB_PASSWORD','pwd');
define('DB_DBNAME','doccms');
define('TB_PREFIX','doc_');
//模板配置字段
define('WEBOPEN',true);
define('WEBSIZE','500');
define('WEBSIZECOUNTS','23');
define('WEBURL','www.doccms.com');
define('SITENAME','稻壳企业建站系统[DocCms X1.0]2013正式版');
define('SITEKEYWORDS','');
define('SITESUMMARY','');
define('HTMLPATH','/html');
define('UPLOADPATH','/upload/');
define('TIMEZONENAME','8');
define('STYLENAME','doccms_model_1');
define('URLREWRITE',false);
define('CACHETIME','0');
//评论审核
define('COMMENTAUDITING',false);
//留言审核
define('GUESTBOOKAUDITING',true);

define('SKINROOT','skins');
//系统开始运行时间
define('doccmsbirthday','now');//此处如无需要请不要随意更改，否则将无法正常运行
//编辑器
define('EDITORSTYLE','kindeditor');
//默认设置
define('ABSPATH',dirname(__FILE__).'/../');
define('ROOTPATH','root'); //类似于 /xmlol (注意后面不带 /)
define('VERSION','X2013 1.1.0101');
$fileIndex 	= 'index.html';
$fileCommon = 'common.html';
//水印图片
define('ISWATER',true);
define('WATERIMGS','/inc/img/system/doccms.png');
//缩略图背景颜色设置
define('paint_bgcolor','0xffffff');
//首页调用模板图片的默认尺寸
define('articleWidth','140');
define('articleHight','105');
define('listWidth','140');
define('listHight','105');
define('productWidth','140');
define('productHight','105');
define('pictureWidth','140');
define('pictureHight','105');
//列表模块页缩略图大小设置

define('moduleUserWidth','120');
define('moduleUserHight','120');
/*上传图片的大小设置*/
define('productMiddlePicWidth','560');
define('productMiddlePicHight','420');
define('productSmallPicWidth','300');
define('productSmallPicHight','225');
define('pictureMiddlePicWidth','560');
define('pictureMiddlePicHight','420');
define('pictureSmallPicWidth','300');
define('pictureSmallPicHight','225');
define('videoWidth','300');
define('videoHight','225');
define('userWidth','120');
define('userHight','120');
define('linkersWidth','90');
define('linkersHight','30');
//内容页列表模块单页显示默认条数
define('listCount','12');
define('pictureCount','12');
define('productCount','6');
define('videoCount','12');
define('guestbookCount','10');
define('commentCount','6');
define('jobsCount','6');
define('calllistCount','6');
define('downloadCount','10');
//后台登录绑定IP
define('LOGINIP','');
//SMTP邮箱设置
define('productISON',false);
define('orderISON',false);
define('guestbookISON',false);
define('smtpPort','');
define('smtpServer','');
define('smtpId','');
define('smtpPwd','');
define('smtpSender','');
define('smtpReceiver','');
//支付宝设置
define('PAY_ISPAY','0');
define('PAY_ISJS','1');
define('PAY_PARTNER','');
define('PAY_KEY','');
define('PAY_SELLER','');
define('PAY_SHOW_URL','');
define('PAY_MAINNAME','');
//财付通设置
define('PAY_ISPAY_TEN','0');
define('PAY_ISJS_TEN','1');
define('PAY_PARTNER_TEN','');
define('PAY_KEY_TEN','');
define('PAY_SELLER_TEN','');
define('PAY_SHOW_URL_TEN','');
define('PAY_MAINNAME_TEN','');
function get_skin_root()
{
	return ROOTPATH.'/'.SKINROOT.'/'.STYLENAME.'/';
}
function get_abs_skin_root()
{
	return ABSPATH.'/'.SKINROOT.'/'.STYLENAME.'/';
}
function get_root_path()
{
	return ROOTPATH;
}