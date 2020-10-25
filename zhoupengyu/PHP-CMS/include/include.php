<?php
require( 'config/config.php');
require( 'config/db.php');
require( 'class/GlobalVariant.php');
require( 'class/sqlToolClass.php');
require( 'class/mySql.php');
require( 'smarty/SmartyBC.class.php');
require( 'lib/verify.php');
require( 'lib/ConfigClass.php');
require( 'lib/MessageClass.php');
require( 'language/'._DEFAULT_LANGUAGE.".php");
require( 'framework/coreFramework.php');
if (_DATABASE_OPEN=="1") $db =  new MySqlClass();
$GVar = new GlobalVariant();
$Config = ConfigClass::getConfig();