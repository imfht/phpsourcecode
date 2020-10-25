<?php
/**
 * kephp bootstrap file.
 */

require __DIR__ . '/../vendor/autoload.php';

/** @var \Ke\App $APP */
global $app;

try {
	$app = new \Ke\App(__DIR__);
	$app->getLoader()->loadHelper('string');
} catch (Throwable $throw) {
	print $throw->getMessage();
}
