<?php
if(!defined('DIR')){
	exit('Please correct access URL.');
}

return array(
    'db1' => array(
        'host' => '10.0.0.4',
        'port' => '3306',
        'name' => 'root',
        'pass' => 'root',
        'database' => 'test',
        'prefix' => '',
		'charset' => 'utf8mb4'
    ),
    'db2' => array(
        'host' => '10.0.0.4',
        'port' => '3306',
        'name' => 'root',
        'pass' => 'root',
        'database' => 'test',
        'prefix' => '',
		'charset' => 'utf8mb4'
    ),
);
