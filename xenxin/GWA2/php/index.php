<?php
# the application entry...

# cli
if(0){ # in some scenarios, this should be set as 0 to disable this function globally.
	error_reporting(E_ALL & ~E_NOTICE);
	if($argv && $argc > 0){
		# path
		ini_set('include_path', get_include_path(). PATH_SEPARATOR . dirname($_SERVER['PHP_SELF']));
		ini_set('max_execution_time', 0);
		
		chdir(dirname(__FILE__));
		include("./comm/cmdline.inc.php");
		chdir(dirname(__FILE__));
	}
}

# header.inc file
include("./comm/header.inc.php");

# main logic
$mod = Wht::get($_REQUEST, 'mod'); # which mod is requested?
$act = Wht::get($_REQUEST, 'act'); # what act needs to do?
if($mod == ""){
  $mod = "index";
}

$data['mod'] = $mod;
$data['act'] = $act;
#$data['baseurl'] = $baseurl;
$mod = securityFileCheck($mod); # enhanced, 08:23 Friday, January 10, 2020
if(file_exists("./ctrl/".$mod.".php")){
	include("./ctrl/".$mod.".php");
}
else{
	print "ERROR.";
	error_log(__FILE__.": found error mod:[$mod] 201107080706.");
	exit(0);
}

# something shared across the app
if(true){
  include("./ctrl/include.php");
}

# footer.inc file
include("./comm/footer.inc.php");

?>
