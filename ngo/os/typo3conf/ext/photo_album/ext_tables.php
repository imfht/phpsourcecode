<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Jykj.PhotoAlbum',
            'Album',
            '相册管理'
        );

        $pluginSignature = str_replace('_', '', 'photo_album') . '_album';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:photo_album/Configuration/FlexForms/flexform_album.xml');

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Jykj.PhotoAlbum',
            'Photos',
            '照片管理'
        );

        $pluginSignature = str_replace('_', '', 'photo_album') . '_photos';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:photo_album/Configuration/FlexForms/flexform_photos.xml');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('photo_album', 'Configuration/TypoScript', '相册管理');

    }
);
