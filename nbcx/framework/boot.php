<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//框架常量
define('DS'        ,DIRECTORY_SEPARATOR);
define('__NB__'    ,__DIR__.DS);
define('__APP__'   ,_APP_.DS);

$_ENV['argv'] = isset($argv)?$argv:'';
spl_autoload_register(function($object) {
    $ex = explode('\\',$object);
    if($ex[0]=='nb') {
        $path = str_replace($ex[0], __NB__.'src', $object).'.php';
        $path = str_replace('\\', '/', $path);
    }
    else{
        $path = __APP__.str_replace('\\', '/', $object).'.php';
    }
    if(is_file($path)) {
        return require_once($path);
    }
});
include(__NB__ .'helper.php');
include(__NB__ .'src'. DS . 'Debug.php');
include(__NB__ .'src'. DS . 'Config.php');
