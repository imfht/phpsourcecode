<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-10-25
 * Time: 上午10:10
 */

return [
	'default'=>'sqlite',
	'connections'=>[
		'sqlite'=>[
			'driver'=>'sqlite',
			'database'=>':memory:',
			'prefix'=>''
		]
	]
];