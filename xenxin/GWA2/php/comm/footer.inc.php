<?php

if($isoput){
	if($isheader){
		# other stuff
	}
	$out .= "</body></html>";
}

//- content output
$isOB = 0;
if(ob_start('ob_gzhandler')){ $isOB = 1; }
else if(ob_start()){
    $isOB = 1;
}
if($smttpl != ''){

	$data['smttpl'] = $smttpl;
	$data['url'] = $url;	
	$data['req'] = $_REQUEST; # also see comm/header.inc tag#userdatatovar 
	$data['ses'] = $_SESSION;
	$data['viewdir'] = $rtvviewdir = $rtvdir.'/view/'.$siteid; # where and why?
	$data['rtvdir'] = $rtvdir; # refer view/default/include/sitefeedback.html
	
	# moved in 22:04 10 July 2016 from comm/header
	require($_CONFIG['smarty']."/Smarty.class.php");
	$smt = new Smarty();
	$rtvviewdir = $rtvdir."/view/".$siteid;
	$viewdir = $appdir.'/view/'.$siteid;
	#`print "viewdir:[$viewdir]\n";
	$smt->setTemplateDir($viewdir);
	$smt->setCompileDir($viewdir.'/compile');
	$smt->setConfigDir($viewdir.'/config');
	$smt->setCacheDir($viewdir.'/cache');
	
	# Fri Jul 10 22:17:09 CST 2015
	foreach($_REQUEST as $k=>$v){
		if(!array_key_exists($k, $data)){
			$data[$k] = $v;	
		}	
	}
	foreach($data as $k=>$v){ # main point
		$smt->assign($k, $v);
	}
	
	# template handling
	# try cache tpl content first
	$tplCacheKey = "gwa2_tpl_cache_key_main_".$viewdir."_"; $tplCacheExpire = $_CONFIG['cacheexpire']; # 5 mins?
	$tplCacheReady = true; $tplTmpTag = ".tmp";
	$enableTplCache = false;
	if(!$_CONFIG['is_debug'] && $_CONFIG['enable_cache']){ $enableTplCache = true; }
	else{ $tplCacheReady = false; }
	if($enableTplCache){
		$tplCache = $user->getBy("cache:", "", $args=array("key"=>$tplCacheKey));
		if(!$tplCache[0]){
			$tplCacheReady = false;
			debug("comm/footer: read tpl cache fail.".serialize($tplCache));
		}
		if(tplCacheReady){
			$tplCache = $user->getBy("cache:", "", $args=array("key"=>$tplCacheKey.$smttpl));
			if(!$tplCache[0]){
				$tplCacheReady = false;
				debug("comm/footer: read tpl cache fail.".serialize($tplCache));
			}
		}
	}
	# real actions for replacements in tpl
	if(!$tplCacheReady){
		$s_indextpl = $viewdir."/index.html";
		$s_smttpl = $viewdir."/".$smttpl;
		$indexcontent = file_get_contents($s_indextpl);
		$tplcontent = file_get_contents($s_smttpl);
		$resrclist = array("images","css","js","pics", "styles", "scripts");
		foreach($resrclist as $k=>$v){
			$indexcontent = preg_replace("/\"$v\//", "\"".$reqdir."/view/".$siteid."/$v/", $indexcontent);
			$tplcontent = preg_replace("/\"$v\//", "\"".$reqdir."/view/".$siteid."/$v/", $tplcontent);
		}
		file_put_contents($s_indextpl.$tplTmpTag, $indexcontent);
		file_put_contents($s_smttpl.$tplTmpTag, $tplcontent);
		if($enableTplCache){
			$tplCache = $user->setBy("cache:", $args=array("key"=>$tplCacheKey, "expire"=>$tplCacheExpire, "value"=>1));
			if(!$tplCache[0]){
				debug("comm/footer: set tpl cache fail.".serialize($tplCache));
			}
			$tplCache = $user->setBy("cache:", $args=array("key"=>$tplCacheKey.$smttpl, "expire"=>$tplCacheExpire, "value"=>1));
			if(!$tplCache[0]){
				debug("comm/footer: set tpl cache fail.".serialize($tplCache));
			}
		}
	}
	# for conflicts between smarty {} and javascript {}, using {literal}{/literal}
	if($display_style == $_CONFIG['display_style_index']){
		$smttpl = $smttpl.$tplTmpTag;
		$smt->assign('smttpl', $smttpl);
		$smt->display('index.html'.$tplTmpTag); 
		# use index.html, $smttpl would be embedded in index.html by smarty, updated on Sun Jul 29 09:59:29 CST 2012
	}
	else if($display_style == $_CONFIG['display_style_smttpl']){
		//$smt ->assign('respobj', $data['respobj']);
		$smt->display($smttpl.$tplTmpTag); # use template file only
		//var_dump($data);
	}
	else{
		debug(__FILE__.": Something wrong with display style and smttpl:$smttpl .");				
	}
}
else{
	if(isset($fmt) && $fmt != ''){
		if($fmt == 'json'){
			header("Content-type: application/json;charset=utf-8");
			#print_r($data);
			#print json_encode($data['respobj']);
			$data['out'] = $out;
			$output = json_encode($data); # main point
			$jsonerr = json_last_error();
			if($jsonerr == JSON_ERROR_NONE){
				print $output;
			}
			else{
				$dataX = array('errorcode'=>1606141649, 
					'errordesc'=>'Error found in json_encode for output. json_err_code:['
						.$jsonerr.'] json_err_message:['
						.(function_exists('json_last_error_msg')?json_last_error_msg():'null')
						.'] raw data attached as below.');
					
				print json_encode($dataX);
				print ' '.chr(10).chr(13);
				print "\n\n/*\n\n";
				print_r($data);
				print "\n\n*/";
			}		
		}
        else{
            print $out.="<!-- Unknown fmt:[$fmt] in output, using text/plain as default. 1608032158. -->";
        }
	}
	else{
		#error_log(__FILE__.": smttpl is empty. not display with Smarty.req:[".$_SERVER['REQUEST_URI']);
		print $out;
	}
}
if($isOB){ ob_end_flush(); }

#error_log(__FILE__.": out:[".$out."]");
#error_log(date("m-d H:i:s").": ".__FILE__.": request:[".$_SERVER['REQUEST_URI']."] query_string:[".$_SERVER['QUERY_STRING']."] smttpl:[$smttpl] userid:[$userid] ismaishou:[".$data['ismaishou']."]");
#error_log(__FILE__.": req:[".$user->toString($_REQUEST)."]");
#debug($_REQUEST, 'request');

$gtbl = null;
$user = null;
$smt = null;
$out = null;

#print_r(UID);
#print_r($_SESSION);

?>
