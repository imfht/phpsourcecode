<?php
defined('TYPO3_MODE') || die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'fs_media_gallery',
    'Mediagallery',
    'LLL:EXT:fs_media_gallery/Resources/Private/Language/locallang_be.xlf:mediagallery.title'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['fsmediagallery_mediagallery'] = 'layout,select_key,pages,recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['fsmediagallery_mediagallery'] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'fsmediagallery_mediagallery',
    'FILE:EXT:fs_media_gallery/Configuration/FlexForms/flexform_mediaalbum.xml'
);
