<?php

if(!file_exists(dirname(__FILE__).'/libs/install.lock')){
  header('Location:install/index.php');
  exit();
}
if(!empty($_GET["go"]) && $_GET["go"] =='master'){
	require 'view/master/index.php';
}else{
	require 'libs/function.php';
	require 'view/home/index.php';
}
?>

