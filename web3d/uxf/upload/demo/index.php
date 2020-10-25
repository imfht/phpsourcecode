<?php

define('DISCUZ_ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);//按discuz标准结构

define('DISCUZ_CORE_DEBUG', TRUE);

define('APPTYPEID', 2001); //当前应用ID
define('CURSCRIPT', 'index'); //当前应用名称

define('SUB_DIR', 'demo');

require DISCUZ_ROOT . 'source/fluiex/F.php';

use fluiex\F;

F::start();
F::app()->init();

$mod = F::app()->var;

var_dump($mod);
