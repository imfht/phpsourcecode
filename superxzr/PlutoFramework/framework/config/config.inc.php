<?php
	/**
	  * PlutoFramework
	  */

//Set up an array for constant
$_C = array();

//Database credentials
$_C['DB_HOST'] = '127.0.0.1';
$_C['DB_NAME'] = '';
$_C['DB_USER'] = '';
$_C['DB_PW'] = '';

//General configuration options
$_C['TIMEZONE'] = 'Asia/Shanghai';

//Debug mode
$_C['DEBUG'] = TRUE;

//encoded
$_C['ENCODED'] = 'UTF-8';

//default class name
$_C['CLASS'] = 'Home';

//Converts the constants array into actual constants
foreach($_C as $constant=>$value){
	define($constant,$value);
}