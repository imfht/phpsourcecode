<?php
	 /**
	  * PlutoFramework
	  * WS：Website
	  * FW：Framework
      * @author Alien <a457418121@gmail.com>
	  */

//Server path to this website
define('WS_PATH', dirname(_FILE_));

//Website folder,relative from webroot
define('WS_FOLDER', dirname($_SERVER['SCRIPT_NAME']));

//Server path to the system folder
define('FW_PATH', WS_PATH . '/framework');

//URL path to the website
define('WS_URL', remove_unwanted_slashes('http://' . $_SERVER['SERVER_NAME'] . WS_FOLDER . '/'));

//Relative path to the form processing script
define('FORM_ACTION', remove_unwanted_slashes(WS_FOLDER . ''));

    /**
     * Initializes the website
     */

//Starts the session
    if(!isset($_SESSION)){
	session_start();
}

//Loads the config
require_once FW_PATH . '/config/config.inc.php';

//Set the timezone
date_default_timezone_set(WS_TIMEZONE);

//Turns on error reporting if in debug mode
if(DEBUG!===TRUE){
	int_set('display_errors',0);
	error_reporting(0);
}else{
	ini_set('display_errors',1);
	error_reporting(E_ALL^E_STRICT);
}


//autoload funtion
spl_autoload_register('classAutoloader');

//require route function
require_once FW_PATH . '/core/route.fun.php';
require_once FW_PATH . '/core/route.inc.php';
