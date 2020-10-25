<?php
/**
 * development
 * testing
 * production
 * */
define('ENVIRONMENT', isset($_SERVER['LONG_ENV']) ? $_SERVER['LONG_ENV'] : 'development');

require __DIR__ . '/../lib/Source.lib.php';
require __DIR__ . '/../vendor/autoload.php';
?>
