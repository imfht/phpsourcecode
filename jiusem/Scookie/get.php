<?php 
require('Scookie.class.php');

$userInfo = Scookie::get('user');
print_r($userInfo);
?>