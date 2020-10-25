<?php

if (!\defined('TYPO3_MODE')) {
	die('Access denied.');
}

// new cache table
if (!\is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['lfeditor_select_options_cache'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['lfeditor_select_options_cache'] = array();
}

$extConf = \SGalinski\Lfeditor\Utility\ExtensionUtility::getExtensionConfiguration();
if (version_compare(\TYPO3\CMS\Core\Utility\VersionNumberUtility::getCurrentTypo3Version(), '9.0.0', '<')) {
	$pathSite = PATH_site;
} else {
	$pathSite = \TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/';
}

if (
	$extConf['pathAdditionalConfiguration'] !== 'typo3conf/AdditionalConfiguration.php' &&
	\is_file($pathSite . $extConf['pathAdditionalConfiguration'])
) {
	// Include the additional LFEditor configuration file if not set to AdditionalConfiguration.php
	include_once $pathSite . $extConf['pathAdditionalConfiguration'];
}
unset($extConf);
