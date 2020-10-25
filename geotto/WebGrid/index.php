<?php
$file = isset($_GET['file'])?$_GET['file']:"user_controller";
$class = isset($_GET['class'])?$_GET['class']:"UserController";
$fun = isset($_GET['fun'])?$_GET['fun']:"index";
$modelPath = "models";
$viewPath = "views";

require_once("mysql_connect.php");

include_once("{$file}.php");
$controller = new $class($modelPath, $viewPath, $dbc);
$controller->$fun();

mysqli_close($dbc);
?>
