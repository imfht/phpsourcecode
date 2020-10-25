<?php

# Run php script from cmd line support without web server
# By wadelau, 20:07 26 May 2016
# This func is a PHP-specified, if deploy -GWA2 in other programming languages, e.g. Java, Python, Perl, the module should also be rewrited according to new requirements.

if(1){ # in some scenarios, this should be set as 0 to disable this function globally.
	
	if($argv && $argc > 0){ # run from cmd line?
		error_log(__FILE__.": run from cmd line. argc:[".$argc."]");
		# e.g.  A-style
		# /usr/local/php/bin/php -c /www/bin/php/php.ini "/path/to/index.php" "mod=mob" "act=get_offer" "fmt=json"
		# or B-style
		# /usr/local/php/bin/php -c /www/bin/php/php.ini "/path/to/index.php" "?mod=mob&act=get_offer&fmt=json"
		# $_REQUEST
		foreach ($argv as $arg) {
			$qPos = strpos($arg, '?');
			if( $qPos !== false || strpos($arg, '&') !== false){ # B-style
				if($qPos == 0){ $arg = substr($arg, 1);}
				$argArr = explode('&', $arg);
				foreach($argArr as $ek=>$ev){
					$eArr = explode('=', $ev);
					$_REQUEST[$eArr[0]] = $eArr[1];
				}
			}
			else{ # A-style
				$e=explode("=",$arg);
				if(count($e)==2){
					$_REQUEST[$e[0]]=$e[1];
				}
				else{
					$_REQUEST[$e[0]]=0;
				}
			}
		}
		#print_r($_REQUEST);
		
		# set utf-8 for environment
		$myCharset = 'UTF-8';
		if(function_exists('mb_internal_encoding')){
		    mb_internal_encoding($myCharset);
		    mb_http_output($myCharset);
		    mb_http_input($myCharset);
		    mb_regex_encoding($myCharset);
		}
		else{
		    error_log(__FILE__." cli mode with no mb_internal_encoding. 1705112102.");
		}
		
		# $_SERVER
		empty( $_SERVER['HTTP_HOST'] ) && $_SERVER['HTTP_HOST'] = 'localhost';
		empty( $_SERVER['REQUEST_URI'] ) && $_SERVER['REQUEST_URI'] = '/';
		empty( $_SERVER['DOCUMENT_ROOT'] ) && $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
		empty( $_SERVER['HTTP_REFERER'] ) && $_SERVER['HTTP_REFERER'] = '';
		empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) && $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en_US';
		empty( $_SERVER['REDIRECT_URL'] ) && $_SERVER['REDIRECT_URL'] = '';
		#print_r($_SERVER);
		
		error_reporting(E_ERROR | E_WARNING | E_PARSE);

	}
	
	#exit("Test point reached. line:[".__LINE__."] file:[".__FILE__."].");
	
}

?>