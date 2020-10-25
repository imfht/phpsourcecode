<?php

global $sid, $appdir, $siteid, $user, $userid, $isdbg, $lang;
date_default_timezone_set("Europe/London"); # +0000
session_start(); # in developping stage, using php built-in session manager

# dir manipulate
$docroot = $_SERVER['DOCUMENT_ROOT'];
$rtvdir = dirname(dirname(__FILE__)); # relative dir
$rtvdir = str_replace($docroot,"", $rtvdir);
$reqdir = substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/')); # for tpl in footer.inc?
$appdir = $docroot.$reqdir;

if($appdir == ''){
    $appdir = $rtvdir;
}
#print "docroot:[$docroot] appdir:[$appdir] rtvdir:[$rtvdir] req_uri:[".$_SERVER['REQUEST_URI']
#."] req_dir:[".substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '/'))."]";
#exit(0);

require($appdir."/inc/config.class.php");
require($appdir."/mod/user.class.php");
require($appdir."/comm/tools.function.php");

$siteid = $_CONFIG['siteid']; $isdbg = $_CONFIG['is_debug'];
if($isdbg){
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

# user info
define('UID',$_CONFIG['agentalias'].'_user_id');
global $data; $data=array(); # variables container for template file

$HTTP_REFERER = parse_url($_SERVER['HTTP_REFERER']);
if (! isset($_SESSION['ref']) || $HTTP_REFERER['path'] != $_SERVER['REDIRECT_URL']){
    $_SESSION['ref'] = array(
        'REDIRECT_URL'=>$_SERVER['REDIRECT_URL'],
        'HTTP_REFERER'=>"{$_SERVER['HTTP_REFERER']}"
    );
}

if(!isset($user)){
    $user = new User();
}
$userid = '';
if(array_key_exists(UID, $_SESSION) && $_SESSION[UID] != ''){
	$userid = $_SESSION[UID];
	$user->setId($userid);
	$data['islogin'] = 1;
	$data['userid'] = $userid;
	$data['username'] = $user->getUserName();
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
	debug("comm/header: ilang:".$lang->getTag()." welcome:".$lang->get("welcome"));
	$data['lang']['welcome'] = $lang->get('welcome');
	$data['lang']['agentname'] = $lang->get('lang_agentname');
	$data['lang']['appchnname'] = $lang->get('lang_appchnname');
	//- set to cookie if necessary, @todo
}
//- optional
if($_REQUEST['lang'] != ''){
	$sid = Wht::get($_REQUEST, 'sid').'.'.Wht::get($_REQUEST, 'lang');	
}

# page header format
$fmt = Wht::get($_REQUEST, 'fmt');
if(isset($fmt) && $fmt != ''){
    if($fmt == 'json'){
        header("Content-type: application/json;charset=utf-8");
    }
    else if($fmt == 'xml'){
        header("Content-type: text/xml;charset=utf-8");
    }
    else{
        //- @todo
    }
}
else{
    header("Content-type: text/html;charset=utf-8");
}

# main content container if no template file
$out = '';

# smarty template engine, relocated into comm/footer
$smttpl = '';

global $display_style;
$display_style = $_CONFIG['display_style_index'];

# convert user input data to variables, tag#userdatatovar
foreach($_REQUEST as $k=>$v){
    $k = trim($k);
    if($k != ''){
        if(preg_match("/([0-9a-z_]+)/i", $k, $matcharr)){
            $k = $matcharr[1];
			if(is_string($v)){
				$v = trim($v);
				if(stripos($v, "<") > -1){
				    # <script , <embed, <img, <iframe, etc.  Mon Feb  1 14:48:32 CST 2016
					$v = str_ireplace("<", "&lt;", $v);
					$_REQUEST[$k] = $v;
				}
			}
            $data[$k] = $v;
            if(true)){
                #eval("\${$k} = \"$v\";"); # risky
				${$k} = $v;
            }
        }
		else{
        }
  	}
}

## RESTful handler
$entry_tag = $_CONFIG['entry_tag'];
if($entry_tag != ''){
	$paraArr = explode("/", $_SERVER['REQUEST_URI']);
	$found_entry = 0; $query_string = '';
	$paraCount = count($paraArr);
	for($i=0; $i<$paraCount; $i++){
		if($paraArr[$i] == $entry_tag){
			$found_entry = 1;
		}
		else{
			if($found_entry == 1 && $paraArr[$i] != ''){
				$_REQUEST[$paraArr[$i]] = $paraArr[++$i];
				$query_string .= $paraArr[$i-1]."=".$paraArr[$i].'&';
			}
		}
	}
	if($query_string != ''){ $query_string = substr($query_string, 0, strlen($query_string)-1); }
	$_SERVER['QUERY_STRING'] = $query_string;
}
$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];

$url = '';
if($entry_tag != ''){
	$url = $rtvdir.'/'.$entry_tag;
}
else{
	$url = $rtndir.'?';
}
$data['randi'] = rand(10000,999999);

# global variables
if(true){
    $sid = Wht::get($_REQUEST, 'sid');
    if($sid == ''){
      $sid = Wht::get($_SESSION, 'sid');
      if($sid == ''){
		  #$sid = date("Ymd", time()); # for production
		  $sid = rand(1000, 999999); $_SESSION['sid'] = $sid; # for development
      }
    }
    else{
        #$sid = str_replace('<', '&lt;', $sid); # remedy on Wht::get
    }
    if($entry_tag != ''){
    	$url .= "/sid/".$sid;
    }
    else{
    	$url .= '&sid='.$sid;
    }
}

//-
function exception_handler($exception) {
	echo '<div class="alert alert-danger">';
	echo '<b>Fatal error</b>:  Uncaught exception \'' . get_class($exception) . '\' with message ';
	echo $exception->getMessage() . ' .<br/> <!--- please refer to server log. --> Please report this to administrators.';
	error_log($exception->getTraceAsString());
	error_log("thrown in [" . $exception->getFile() . "] on line:[".$exception->getLine()."].");
	echo '</div>';
}
set_exception_handler('exception_handler');
//-
//- securityFileCheck , 
//- Xenxin@ufqi
// 11:17 Thursday, December 19, 2019
function securityFileCheck($fv){
	$rtn = $fv;
	$rtn = realpath($rtn);
	$badChars = array(';', '%3B', ' ', "%20", '&', "%26", "..", "//", './', "\\", '\.');
	$rtn = str_replace($badChars, '', $rtn);
	if(!preg_match('/^(?:[a-z0-9_\-\/#~]|\.(?!\.))+$/iD', $rtn)){
		$rtn = '';
	}
	if(strpos($rtn, '/') === 0 && !inList($rtn, '/www,/var,/tmp,/var')){
		$rtn = '';
	}
	#debug('comm/header: fv:'.$fv.'->'.$rtn);
	return $rtn;
}
?>
