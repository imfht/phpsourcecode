<?php 

//$_SESSION['auth'] = null;

clean_remember_password();

session_destroy();
header('Location:?c=user&a=login');
die;

