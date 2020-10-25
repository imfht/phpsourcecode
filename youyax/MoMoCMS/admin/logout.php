<?php
session_start();
if(!empty($_SESSION['momocms_admin'])){
	unset($_SESSION['momocms_admin']);
	header("Location:./index.php");
}
?>