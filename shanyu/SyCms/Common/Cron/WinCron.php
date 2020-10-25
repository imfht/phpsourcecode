<?php
	if(PHP_SAPI != 'cli') die;

	chdir('../../');
	define('APP_MODE','api');

	define('WINCRON',TRUE);

	require getcwd().'/index.php';