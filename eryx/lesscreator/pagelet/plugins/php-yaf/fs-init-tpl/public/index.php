<?php

define("APP_PATH",  dirname(dirname(__FILE__)));

$app  = new Yaf_Application(APP_PATH . "/conf/application.ini");
$app->bootstrap() //call bootstrap methods defined in Bootstrap.php
    ->run();

