<?php

$smarty_sysplugin_dir = __DIR__ . '/libs/sysplugins/';
$to_base_dir = __DIR__  . '/smarty-yaf/';

$flags = FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS;
foreach (new RecursiveDirectoryIterator($smarty_sysplugin_dir, $flags) as $fileInfo) {
	$from_path = $fileInfo->getPathname();
    $to_file_name = str_replace(' ', '/', ucwords(str_replace('_', ' ', $fileInfo->getFilename())));
	$to_file_name = str_replace('Smarty/', '', $to_file_name);
	$to_path = $to_base_dir. $to_file_name;
	$to_dir = dirname($to_path);
	if(!is_dir($to_dir)) {
		mkdir($to_dir, 0775, true);
	}
	
	copy($from_path, $to_path);
	
	echo $to_path . "\r\n";
}

