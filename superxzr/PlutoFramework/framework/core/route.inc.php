<?php
	/**
	  * PlutoFramework
    * @author Alien <a457418121@gmail.com>
	  */

//parses the URI
$uriArray = parseUri();
$className = getControllerName($uriArray);

if(empty($className)){
    $className = "$_C['CLASS']";
}

//Initalize the requested view,or throws a error
try{
    $controller = new $className($uriArray);
}catch (exception $error){
    $uriArray[1] = $error->getMessage();
    $controller = new Error($uriArray);
}

/*
 * Output the view
 */

include_once FW_PATH . '/inc/header.inc.php';

$controller->outputView();

include_once FW_PATH . '/inc/footer.inc.php';
