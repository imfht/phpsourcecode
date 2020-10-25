<?php
define('DEBUG', true);

require_once './source/class/class_core.php';

C::app()->init();

C::import('function/uxf');

runMvc();