<?php
defined('TYPO3_MODE') || die();

// Media Gellery typoscript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'fs_media_gallery',
    'Configuration/TypoScript',
    'Media Gallery'
);
// Add Theme 'Bootstrap3'
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'fs_media_gallery',
    'Configuration/TypoScript/Themes/Bootstrap3',
    'Media Gallery Theme \'Bootstrap3\''
);