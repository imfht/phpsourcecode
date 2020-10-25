<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 15/11/7
 * Time: 10:11
 */

include_once "../zeus/Zeus.php";

use zeus\Router;

$route = new Router();
$route->all("*",function(){

    echo 1;

});

$route->dispatch();