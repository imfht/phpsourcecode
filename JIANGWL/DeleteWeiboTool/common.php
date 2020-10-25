<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2016/9/12
 * Time: 17:52
 */
//不超时
set_time_limit(0);
require_once('Config/config.php');
require_once('Config/database.php');
if($config['debug']===TRUE) {
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}


//根目录
define('BASEPATH', !empty($config['base_path']) ? $config['base_path'] : str_replace('\\', '/', realpath(dirname(__FILE__) . '/')) . "/");

//根目录
define('BASEURL', !empty($config['base_url']) ? $config['base_url'] : '/');

//cookie路径
define('COOKIEPATH', BASEPATH . (!empty($config['cookie_path']) ? $config['cookie_path'] : 'cache/cookie/'));

require_once(BASEPATH . 'global.php');

require_once(BASEPATH . 'Loader.php');
require_once(BASEPATH . 'Factory.php');

require_once(BASEPATH . 'functions.php');

//新建loader
Factory::setLoader(new Loader($config));


