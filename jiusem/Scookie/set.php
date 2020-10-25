<?php  

require('Scookie.class.php');

$userInfo = array(
	'id' => 1,
	'name' => 'crazymus'
);

Scookie::set('user',$userInfo,array(
	'expire' => time()+3600
));

echo 'success';




?>