<?php
defined('TYPO3_MODE') || die();


$GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-mediagal'] =
    'apps-pagetree-folder-contains-mediagal';

// Add module icon for Folder (page-contains)
$GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
    'MediaGalleries',
    'mediagal',
    'apps-pagetree-folder-contains-mediagal'
];
