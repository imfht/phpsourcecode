<?php

if(version_compare(PHP_VERSION,'5.3.0','<'))die('You Need PHP Version > 5.3.0 ! , You PHP Version = ' . PHP_VERSION);

define('INDEX_PATH' , str_replace('\\', '/', dirname(__FILE__)).'/');
define('DEBUG'      ,true);
require './HY/HY.php';
