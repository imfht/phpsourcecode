<?php
require_once  'medoo.php';
use Medoo\Medoo;
$database = new Medoo([
	'database_type' => 'mysql',
 	'database_name' => '~dbname~',
	'server' => '~dbhost~',
	'username' => '~dbuser~',
	'password' => '~dbpwd~',
	'charset' => 'utf8',
	//'port' => '~dbport~',
	'logging' => true,
	
]);
?>