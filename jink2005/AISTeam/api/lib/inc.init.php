<?php
// turn off errors on prod
ini_set('display_errors', 'On');

ini_set("arg_separator.output", "&amp;");
ini_set('default_charset', 'utf-8');

session_start();
// get full path to 2-plan
define("CL_ROOT", realpath(dirname(__FILE__) . '/../../' ));
// configuration to load
define("CL_CONFIG", "standard");
// 2-plan version
define("CL_VERSION", 0.6);
// uncomment for debugging
//error_reporting(E_ALL | E_STRICT);
// include config file , pagination and global functions
require(CL_ROOT . "/config/" . CL_CONFIG . "/config.php");
require(CL_ROOT . "/include/SmartyPaginate.class.php");
require(CL_ROOT . "/include/initfunctions.php");
// Start database connection
if (!empty($db_name) and !empty($db_user))
{
    $tdb = new datenbank();
    $conn = $tdb->connect($db_name, $db_user, $db_pass, $db_host);
}
// get the available languages
$languages = getAvailableLanguages();
// get URL to 2-plan
$url = getMyUrl();

// Assign globals to all templates
if (isset($_SESSION["userid"]))
{
    // unique ID of the user
    $userid = $_SESSION["userid"];
    // name of the user
    $username = $_SESSION["username"];
    // timestamp of last login
    $lastlogin = $_SESSION["lastlogin"];
    // selected locale
    $locale = $_SESSION["userlocale"];
    // gender
    $gender = $_SESSION["usergender"];
    // what the user may or may not do
    $userpermissions = $_SESSION["userpermissions"];
}
else
{
    $loggedin = 0;
}
// get system settings
if (isset($conn))
{
    $set = (object) new settings();
    $settings = $set->getSettings();
    define("CL_DATEFORMAT", $settings["dateformat"]);

    date_default_timezone_set($settings["timezone"]);
}

if (!isset($locale))
{
    if (isset($settings["locale"]))
    {
	$locale = $settings['locale'];
    }
    else
    {
	$locale = "en";
    }
    $_SESSION['userlocale'] = $locale;
}
// if detected locale doesnt have a corresponding langfile , use system default locale
// if, for whatever reason, no system default language is set, default to english as a last resort
if (!file_exists(CL_ROOT . "/language/$locale/lng.conf"))
{
    $locale = $settings['locale'];
    $_SESSION['userlocale'] = $locale;
}
