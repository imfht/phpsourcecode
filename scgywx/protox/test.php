<?php
define('ROOT', dirname(__FILE__));
require('protox.php');
protox::init(array(
	'path' => ROOT . '/protocol/',
));

echo "<pre>";
echo "===============signle object================\n";
$input = array(
	'name' => 'test',
	'age' => '123a',
	'qq' => 123456,
	'phone' => 111,
	'family' => array(
		array(
			'type' => 'father',
			'name' => "test's Dad",
		),
		array(
			'type' => 'mother',
			'name' => "test's Mam"
		)
	)
);
$output = protox::make('person', $input);
var_dump($output);

echo "===============list object================\n";
$input = array(
	array(
		'name' => 'abc',
		'age' => 111,
	),
	array(
		'name' => 'fuck',
		'age' => 999
	)
);
$output = protox::make('person_list', $input);
var_dump($output);