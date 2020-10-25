<?php
/**
 * kephp global cli entry file.
 */

if (PHP_SAPI !== 'cli')
	exit('This file can only be used in CLI mode!');

function loadAppKephp()
{
	$useGlobalKephp = !empty(getenv('KEPHP_GLOBAL'));

	if ($useGlobalKephp) return false;

	$cwd = getcwd();
	$isLoad = false;
	if ($cwd === __DIR__)
		return false;
	foreach (['ke.php'] as $file) {
		$file = $cwd . '/' . $file;
		if (is_file(($file))) {
			$isLoad = realpath($file);
			require $file;
		}
	}
	return $isLoad;
}

if (loadAppKephp() === false) {
	require __DIR__ . '/src/Ke/App.php';

	$app = new \Ke\App(__DIR__);

	\Ke\Cli\Console::getConsole()->seekCommand()->execute();
}
