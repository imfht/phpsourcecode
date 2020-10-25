<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
	$_EXTKEY,
	'phpexcel',
	'tx_phpexcel_service',
	array (
		'title' => 'PHPExcel for TYPO3',
		'description' => '',
		'subtype' => '',
		'available' => TRUE,
		'priority' => 50,
		'quality' => 50,
		'os' => '',
		'exec' => '',
		'classFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Classes/Service/Phpexcel.php',
		'className' => 'ArminVieweg\PhpexcelService\Service\Phpexcel',
	)
);