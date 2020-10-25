<?php
/*******************************************************************************
* Filename    : language.php
* Description : islaVista language switch.
* Created on  : 03/01/04
* CVS Version : $Id: language.php,v 1.27.28.4 2009/11/04 08:22:09 anniew Exp $
* refined by wadelau@ufqi
******************************************************************************/

require('lang/langList.php');

if(!empty($_GET['language'])){
	$language=$_GET['language'];
	//$_SERVER['HTTP_ACCEPT_LANGUAGE'] = $language;
}
else if(!empty($_COOKIE['language'])){
	$language=$_COOKIE['language'];
	//$_SESSION['language'] = $language;
}
else{
	//print_r($_SERVER['HTTP_ACCEPT_LANGUAGE']);
	preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
	$lang=$matches[1];
	switch(substr($lang,0,2)){
		case 'en':
			$language='en_US';
			break;
		case 'zh':
			$language='zh_CN';
			break;
		default:
			$language='en_US';
			break;
	}
}

$language=str_replace('/','',$language);
$defaultpage='index';
$langmodule=file2mod($defaultpage);

$langupdate = false;
if(empty($_SESSION['langprops']) || empty($_SESSION['language']) || $language!=$_SESSION['language']){
	$langupdate = true;
	$_SESSION['language'] = $language;
}

//- load language properties
if($langupdate){
	if(isset($_SESSION['language']) && $_SESSION['language']=="en_US"){
		$fprop=fopen('lang/en_US_Messages.properties','r');
	}
	else  if(isset($_SESSION['language']) && $_SESSION['language']=="zh_CN"){
		$fprop=fopen('lang/zh_CN_Messages.properties','r');
	}
	else{
		$fprop=fopen('lang/zh_CN_Messages.properties','r');
	}

	while($fprop!=NULL&&!feof($fprop)){
	  $line=trim(fgets($fprop));
	  if(empty($line)||$line[0]=='#'){
		continue;
	  }
	  $list=explode('=',$line,2);
	  if(count($list)==2) {
		$tags=explode('.', trim($list[0]),4);
		$taglen=count($tags);
		if ($taglen>2&&$tags[0]=='v'){
			if($taglen==4){
			  $langprops[$tags[1]][$tags[2]][$tags[3]]=str_replace('\\\\','\\',trim($list[1]));
			}
			else{
			  $langprops[$tags[1]][$tags[2]]=str_replace('\\\\','\\',trim($list[1]));
			}
		}
	  }
	}

	if ($fprop!=NULL){ fclose($fprop); }
	$_SESSION['langprops'] = $langprops;
	
}

$langprops = $_SESSION['langprops'];

# 
# refined by wadelau@ufqi
#
#function gprop($tag,$group=null){
function lclang($tag, $group=null){ 
	# local language, output in this app, will be called in html tpl as
	# <li><a {if $mod eq "index"} class="active"{/if} href="{$url}">{lclang("home")}</a></li> 
	
	global $langprops,$langmodule;

	if(empty($group)) {
		if (isset($langprops[$module][$tag])){
		  return $langprops[$module][$tag];
		}
		else if (isset($langprops['common'][$tag])){
		  return $langprops['common'][$tag];
		}
		else{ 
		  return NULL;
		}
	}
	else {
		if (isset($langprops[$module][$group][$tag])){
		  return $langprops[$module][$group][$tag];
		}
		else if (isset($langprops['common'][$group][$tag])){
		  return $langprops['common'][$group][$tag];
		}
		else{
			return NULL;
		}
	}
	
}

if(!headers_sent()){
	//header('Content-type: text/html; charset='.$language);
	setcookie('language',$language,time()+15552000);
}
else{
	#debug('lang set fail for cannot modify header information.');
}

?>
