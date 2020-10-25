<?php
/**
 * Created by PhpStorm.
 * @author Luficer.p <81434146@qq.com>
 * Date: 16/11/3
 * Time: 上午10:36
 */
define("BASE_DIR",preg_replace("#public#","",__DIR__));
define('TINYMVC_DEBUG',true);

require BASE_DIR."vendor/autoload.php";

use \LuciferP\TinyMvc\app\Application as App;

App::instance()->run();

