<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'TaoJiang.' . $_EXTKEY,
	'News',
	array(
		'News' => 'list, new, create, edit, update, delete, multidelete',
		
	),
	// non-cacheable actions
	array(
		'News' => 'list, new, create, edit, update, delete, multidelete',
		
	)
);

//\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter('TaoJiang\\NewsFrontEdit\\Property\\TypeConverter\\UploadedFileReferenceConverter');
//\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter('TaoJiang\\NewsFrontEdit\\Property\\TypeConverter\\ObjectStorageConverter');

