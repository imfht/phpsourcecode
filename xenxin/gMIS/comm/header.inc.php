<?php
//- embedded in app entry

global $appdir, $userid, $user, $gtbl, $out, $data, $lang;
date_default_timezone_set("Asia/Hong_Kong"); # +0800

$docroot = $_SERVER['DOCUMENT_ROOT'];
$rtvdir = dirname(dirname(__FILE__)); # relative dir
$rtvdir = str_replace($docroot, "", $rtvdir);
$appdir = $docroot.$rtvdir;
if(false){ //- due to soft links in os
	$appdir = $docroot;
	$dirArr = explode("/", $rtvdir);
	$rtvdir = "/".$dirArr[count($dirArr)-1];
	$appdir .= $rtvdir;
}
if($rtvdir == ''){
	$tmpDirArr = explode("/", $_SERVER['PHP_SELF']);
	$rtvdir = '/'.$tmpDirArr[1];
	$tmpDirArr = null;
}
#print "docroot:[$docroot] rtvdir:[$rtvdir] appdir:[$appdir].";
#exit(0);

$dirArr = explode("/", $rtvdir);
$shortDirName = $dirArr[count($dirArr)-1];
# the name of gMIS subdir, i.e. admin, mgmt ...Sat May 23 22:43:21 CST 2015

require_once($appdir."/inc/config.class.php");
$is_debug = $_CONFIG['is_debug'];
if($is_debug){
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
	error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
	error_reporting(-1);
	ini_set('error_reporting', E_ALL ^ E_NOTICE);
	ini_set("display_errors", 1);
}
else{
    header("Cache-Control: public, max-age=604800"); # a week?
    error_reporting(E_ERROR | E_PARSE);
    ini_set('error_reporting', E_ERROR | E_PARSE);
    ini_set("display_errors", 0);
}

require_once($appdir."/class/user.class.php");
require_once($appdir."/comm/tools.function.php");
require($appdir."/class/gtbl.class.php");
require($appdir."/class/pagenavi.class.php");
require_once($appdir."/class/base62x.class.php");
require_once($appdir."/class/language.class.php");

#session_start();
# in initial stage, using php built-in session manager
# implemented with no storage session by Xenxin@ufqi.com, Tue, 7 Mar 2017 22:54:31 +0800
#const UID = 'UID'; const SID = 'SID';
define("UID", $_CONFIG['agentalias'].'_user_id');
define("SID", 'sid');
$_CONFIG['client_ip'] = Wht::getIp();
# imprv4ipv6
$_CONFIG['is_ipv6'] = strpos($_CONFIG['client_ip'], ':')>0 ? 1 : 0;
#debug("comm/header: is_ipv6:".$_CONFIG['is_ipv6']." ip:".$_CONFIG['client_ip']);

if(!isset($user)){
    $user = new User();
    $user->setTbl($_CONFIG['tblpre']."info_usertbl");
}

$userid = ''; $out = ''; $htmlheader = ''; $data = array();
$reqUri = $_SERVER['REQUEST_URI'];
$reqUri = startsWith($reqUri, '/') ? $reqUri : '/'.$reqUri;
$reqUri = str_replace('jdo.php', 'ido.php', $reqUri);
$thisUrl = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$reqUri}";

$sid = Wht::get($_REQUEST, SID);
if(true){
	$dotPos = strpos($sid, '.');
	if($dotPos > 0){
		$tmpArr = explode('.', $sid);
		$pureSid = $tmpArr[0];
		$_REQUEST['sid'] = $pureSid;
		if(!isset($_REQUEST['lang'])){ $_REQUEST['lang'] = $tmpArr[1]; }
	}
}
$userid = $user->getUserBySession($_REQUEST);

//- @todo: workspace id, see inc/config
//
if($userid != ''){
    $user->setId($userid);
}
else if(strpos($_SERVER['PHP_SELF'],'signupin.php') === false
	&& strpos($_SERVER['PHP_SELF'],'readtblfield.php') === false){// ?
    header("Location: ".$rtvdir."/extra/signupin.php?act=signin&bkl=".Base62x::encode($thisUrl));
	exit(0);
}
# language
$ilang = "zh"; 
if(true){
	$icoun = "CN"; $langconf = array();
	$reqtlang = Wht::get($_REQUEST, 'lang');
	if($reqtlang == ''){
		$langs = trim($_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$sepPos = strpos($langs, ',');
		if($sepPos > 0){
			$langs = substr($langs, 0, $sepPos);	
		}
		if(strpos($langs, '-') > 0){
			$tmpArr = explode('-', $langs);
			$ilang = $tmpArr[0]; $tmpArr[1];
		}
		else{
			$ilang = "zh";
		}
	}
	else{
		$ilang = $reqtlang;	
	}
	$langconf['language'] = $ilang;
	$lang = new Language($langconf);
	if($is_debug){ 
		debug("comm/header: ilang:".$lang->getTag()." welcome:".$lang->get("welcome"));
	}
	$data['lang']['welcome'] = $lang->get('welcome');
	$data['lang']['agentname'] = $lang->get('lang_agentname');
	$data['lang']['appchnname'] = $lang->get('lang_appchnname');
	$data['lang']['ilang'] = $ilang;
	//- set to cookie if necessary, @todo
}
if($_REQUEST['lang'] != ''){
	$sid = Wht::get($_REQUEST, 'sid').'.'.Wht::get($_REQUEST, 'lang');	
}

$ido = $rtvdir.'/ido.php?'.SID.'='.$sid;
$jdo = $rtvdir.'/jdo.php?'.SID.'='.$sid;
$url = $rtvdir.'/?'.SID.'='.$sid;

if(!isset($isoput)){
    $isoput = true;
}
# convert user input data to variables, tag#userdatatovar
$base62xTag = 'b62x.';
if(true){
foreach($_REQUEST as $k=>$v){
    $k = trim($k);
    if($k != '' && !inList($k, 'user,lang,userid,appdir,data,out')){
        if(preg_match("/([0-9a-z_]+)/i", $k, $matcharr)){
            $k_orig = $k = $matcharr[1];
			if(is_string($v)){
				$v = trim($v);
				if(stripos($v, "<") !== false){
				    # <script , <embed, <img, <iframe, etc.  Mon Feb  1 14:48:32 CST 2016
					$v = str_ireplace("<", "&lt;", $v);
				}
                if(inString($base62xTag, $v)){
                    if(inString(',', $v)){
                        $tmpArr = explode(',', $v);
                        $v = '';
                        foreach($tmpArr as $tmpk=>$tmpv){
                            if(startsWith($tmpv, $base62xTag)){
                                $tmpv = Base62x::decode(substr($tmpv, 5));
                            }
                            $tmpArr[$tmpk] = $tmpv;
                        }
                        $v = implode(',', $tmpArr);
                    }
                    else{
                        if(startsWith($v, $base62xTag)){
                            $v = Base62x::decode(substr($v, 5)); 
                        }
                    }
                }
				$_REQUEST[$k] = $v;
			}
            $data[$k] = $v;
            if(true){ # preg_match("/[^\x20-\x7e]+/", $v)
                #eval("\${$k} = \"$v\";"); # risky, Tue Aug 28 19:40:10 CST 2018
				${$k} = $v;
			}
			else{
				# @todo
			}
        }
		else{
			# @todo
        }
  	}
	else{
		# @todo
	}
}
}
if(isset($_REQUEST['isoput'])){
    if($_REQUEST['isoput'] == 1){
        $isoput = true;
    }
    else{
        $isoput = false;
    }
}
if(!isset($isheader)){
    $isheader = true;
}
if(isset($_REQUEST['isheader'])){
    if($_REQUEST['isheader'] == 1){
        $isheader = true;
    }
    else{
        $isheader = false;
    }
}

if($isoput){
    if(!$isheader){
		# another place at view/header.html!
        $htmlheader = '<!DOCTYPE html><html lang="'.$ilang.'">
            <head>
            <!-- other stuff in header.inc -->
            <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
            <meta charset="utf-8"/>
			<title>TITLE - '.$_CONFIG['appname'].' -'.$_CONFIG['agentname'].'</title>
			<link rel="stylesheet" type="text/css" href="'.$rtvdir.'/comm/default.css" />
            <script type="text/javascript" src="'.$rtvdir.'/comm/GTAjax-5.7.js" charset=\"utf-8\" async></script>
            <script type="text/javascript" src="'.$rtvdir.'/comm/ido.js?i='
                    .($is_debug==1?rand(0,9999):'').'" charset=\"utf-8\" async></script>
            <script type="text/javascript" src="'.$rtvdir.'/comm/ido_proj.js?i='
                    .($is_debug==1?rand(0,9999):'').'" charset=\"utf-8\" async></script>
            <script type="text/javascript" src="'.$rtvdir.'/comm/popdiv.js" charset=\"utf-8\" async></script>
            <script type="text/javascript" src="'.$rtvdir.'/comm/navimenu/navimenu.js" charset=\"utf-8\" async></script>
			<script type="text/javascript" src="'.$rtvdir.'/comm/Base62x.class.js" charset=\"utf-8\" async></script>
            <link rel="stylesheet" type="text/css" href="'.$rtvdir.'/comm/navimenu/navimenu.css" />
            </head>
            <body> <!--  style="'.($isheader==0?"":"width:880px").'" -->';
    }
    if($isheader){
        if($userid != ''){
            $welcomemsg .= $lang->get('welcome').", ";
            $welcomemsg .= "<a href='".$rtvdir."/ido.php?tbl=info_usertbl&id=".$userid
                ."&act=view' class='whitelink'>";
            $welcomemsg .= $user->getEmail()."</a> !</b>&nbsp; ";
            $welcomemsg .= "<a href=\"".$rtvdir."/extra/signupin.php?act=resetpwd&userid="
                    .$userid."\" class='whitelink'>".$lang->get('user_reset_pwd')."</a>";
            $welcomemsg .= "&nbsp;&nbsp;<select name='langselect' style='background-color:silver;'"
				." onchange=\"javascript:window.location.href='".$url."&lang='+this.options[this.selectedIndex].value;\">
					<option value='en'".($ilang=='en'?' selected':'').">English</option>
					<option value='zh'".($ilang=='zh'?' selected':'').">中文</option>
					<option value='fr'".($ilang=='fr'?' selected':'').">Français</option>
					<option value='ja'".($ilang=='ja'?' selected':'').">日本語</option>
					</select>"
				."&nbsp;&nbsp;<a href=\"".$rtvdir."/extra/signupin.php?act=signout&bkl=".Base62x::encode($thisUrl)
				."\" class='whitelink'>".$lang->get('user_sign_out')."</a> &nbsp;";

            $menulist = '';

            include($appdir."/comm/navimenu/navimenu.php");

            $out .= "<div style=\"width:100%;clear:both\" id=\"navimenu\">".$menulist."</div>";
            //show message number if there are new messages.
            $out .= "<div id=\"a_separator\" style=\"height:10px;margin-top:25px;clear:both\"></div>"
                    ."<!-- height:15px;margin-top:8px;clear:both;text-align:center;z-index:99 -->";
			$data['lang']['copyright'] = $lang->get('copyright');
        }
    }
    else if(!startsWith($act, "modify") && !inString('-addform', $act)
        && !inString("/extra", $thisUrl)){
        $out .= "<style>html{background:white;}</style><!--$thisUrl-->";
    }
}

# initialize new parameters
$i = $j = $id = 0; $randi =0;
$tbl = $field = $fieldv = $fieldargv = $act = '';
$xmlpathpre = $appdir."/xml";
$elementsep = $_CONFIG['septag'];
$db = $_REQUEST['db']; # data db which may differs from $mydb, see ido.php and comm/tblconf.php
$mydb = $_CONFIG['dbname']; # main db on which the app relies
$db = $db=='' ? $mydb : $db;
$tit = $_REQUEST['tit'];
$tbl = $_REQUEST['tbl'];
$tblrotate = $_REQUEST['tblrotate'];
$act = $_REQUEST['act'];
$tit = $tit==''?$tbl:$tit;
$id = isset($_REQUEST['pnskid']) ? $_REQUEST['pnskid'] : $_REQUEST['id'];
$fmt = isset($_REQUEST['fmt'])?$_REQUEST['fmt']:''; # by wadelau on Tue Nov 24 21:36:56 CST 2015
if($fmt == ''){
    header("Content-type: text/html;charset=utf-8");
}
else if($fmt == 'json'){
	header("Content-type: application/json;charset=utf-8");
}
$randi = rand(100000, 999999);

if(strpos($tbl,$_CONFIG['tblpre']) !== 0){
    $tbl = $_CONFIG['tblpre'].$tbl; //- default is appending tbl prefix
}
# tbl test, see inc/webapp.class::setTbl

if(true){ # used in mix mode to cover all kinds of table with or without tbl prefix
	$oldtbl = $tbl;
	#$tbl = (new GTbl($tbl, null, ''))->setTbl($tbl);
	$tmpgtbl = new GTbl($tbl, null, '');
	$tbl = $tmpgtbl->getTbl();
	if($tbl != $oldtbl){
		$_REQUEST['tbl'] = $tbl;
	}
	$tmpgtbl = null;
}

if(isset($_REQUEST['parent'])){
	$tmpnewk = 'pnsk'.$_REQUEST['parent'];
	$_REQUEST[$tmpnewk] = $_REQUEST[$_REQUEST['parent']];
	# print "tmpnewk:[$tmpnewk] value:[".$_REQUEST[$_REQUEST['parent']]."]";
}

# check access control
$superAccess = '';
include($appdir."/act/checkaccess.inc.php");

# template file info
require($_CONFIG['smarty']."/Smarty.class.php");
$smt = new Smarty();
$viewdir = $appdir.'/view';
$smt->setTemplateDir($viewdir);
$smt->setCompileDir($viewdir.'/compile');
$smt->setConfigDir($viewdir.'/config');
$smt->setCacheDir($viewdir.'/cache');
$smttpl = '';

function exception_handler($exception) {
	echo '<div class="alert alert-danger">';
	echo '<b>Fatal error</b>:  Uncaught exception \'' . get_class($exception) . '\' with message ';
	echo $exception->getMessage() . ' .<br/> <!--- please refer to server log. --> Please report this to administrators.';
	# hide sensitive information about server and script location from public.
	error_log($exception->getTraceAsString());
	error_log("thrown in [" . $exception->getFile() . "] on line:[".$exception->getLine()."].");
	echo '</div>';
}
set_exception_handler('exception_handler');

?>
