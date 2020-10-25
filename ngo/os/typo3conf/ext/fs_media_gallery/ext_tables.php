<?php
defined('TYPO3_MODE') || die();

$boot = function ($packageKey) {

    if (TYPO3_MODE === 'BE') {
        // Add CSH
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
            'tt_content.pi_flexform.fsmediagallery_mediagallery.list',
            'EXT:' . $packageKey . '/Resources/Private/Language/locallang_csh_flexforms.xlf'
        );
    }

    /** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'apps-pagetree-folder-contains-mediagal',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        [
            'source' => 'EXT:' . $packageKey . '/Resources/Public/Icons/mediagallery.svg',
        ]
    );
    $iconRegistry->registerIcon(
        'tcarecords-sys_file_collection-folder',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        [
            'source' => 'EXT:' . $packageKey . '/Resources/Public/Icons/mediagallery.svg',
        ]
    );
    $iconRegistry->registerIcon(
        'action-edit-album',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        [
            'source' => 'EXT:' . $packageKey . '/Resources/Public/Icons/mediagallery-edit.svg',
        ]
    );
    $iconRegistry->registerIcon(
        'action-add-album',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        [
            'source' => 'EXT:' . $packageKey . '/Resources/Public/Icons/mediagallery-add.svg',
        ]
    );
    $iconRegistry->registerIcon(
        'content-mediagallery',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        [
            'source' => 'EXT:' . $packageKey . '/Resources/Public/Icons/mediagallery.svg',
        ]
    );
};
$boot($_EXTKEY);
unset($boot);
