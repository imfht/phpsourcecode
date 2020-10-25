<?php
// turn off errors on prod
ini_set('display_errors', 'Off');

ini_set("arg_separator.output", "&amp;");
ini_set('default_charset', 'utf-8');
// Start output buffering with gzip compression and start the session
ob_start('ob_gzhandler');
session_start();
// get full path to 2-plan
define("CL_ROOT", realpath(dirname(__FILE__)));
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
// Start template engine
$template = new Smarty();
// get the available languages
$languages = getAvailableLanguages();
// get URL to 2-plan
$url = getMyUrl();
$template->assign("url", $url);
$template->assign("languages", $languages);
$template->assign("myversion", "0.6.4");
$template->assign("cl_config", CL_CONFIG);
// Assign globals to all templates
if (isset($_SESSION["userid"]))
{
    // unique ID of the user
    $userid = $_SESSION["userid"];
    // name of the user
    $username = $_SESSION["username"];
    // hash of the user
    $userhash = $_SESSION["userhash"];
    // timestamp of last login
    $lastlogin = $_SESSION["lastlogin"];
    // selected locale
    $locale = $_SESSION["userlocale"];
    // gender
    $gender = $_SESSION["usergender"];
    // what the user may or may not do
    $rolesobj = (object) new roles();
    $userpermissions = $rolesobj->getUserRole($userid);
    // logout user if the client is not active
    $clientobj = new client();
    if(!$clientobj->isActive((int)$_SESSION["userid"])) {
        header("Location: manageuser.php?action=logout");
    }
    // assign it all to the templates
    $template->assign("userid", $userid);
    $template->assign("username", $username);
    $template->assign("userhash", $userhash);
    $template->assign("lastlogin", $lastlogin);
    $template->assign("usergender", $gender);
    $template->assign("userpermissions", $userpermissions);
    $template->assign("loggedin", 1);
}
else
{
    $template->assign("loggedin", 0);
}
// get system settings
if (isset($conn))
{
    $set = (object) new settings();
    $settings = $set->getSettings();
    define("CL_DATEFORMAT", $settings["dateformat"]);

    date_default_timezone_set($settings["timezone"]);
    // set iphone template
    if(preg_match('/\/iphone.*/i', $_SERVER["REQUEST_URI"])) {
        $settings[template] = "iphone";
        $settings[template2] = "iphone";
    }
    if(preg_match('/\/bb.*/i', $_SERVER["REQUEST_URI"])) {
       $settings[template] = "iphone";
       $settings[template2] = "bb";
    }
    $template->assign("settings", $settings);
}

// Set Template directory
// If no directory is set in the system settings, default to the standard theme
if (!isset($settings['template']))
    $settings[template] = "standard";
$template->template_dir = CL_ROOT . "/templates/$settings[template]/";
$template->compile_dir = CL_ROOT . "/templates_c/$settings[template]/";
$template->tname = $settings["template"];

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
// Set locale directory
$template->config_dir = CL_ROOT . "/language/$locale/";
// read language file into PHP array
$langfile = readLangfile($locale);
$template->assign("langfile", $langfile);
$template->assign("locale", $locale);
// css classes for headmenue
// this indicates which of the 3 main stages the user is on
$mainclasses = array("desktop" => "desktop",
    "profil" => "profil",
    "admin" => "admin"
    );
$template->assign("mainclasses", $mainclasses);
$they = date("Y");
$them = date("n");
$template->assign("theM", $them);
$template->assign("theY", $they);
// clear session data for pagination
SmartyPaginate::disconnect();

?>