<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Jykj.User',
            'User',
            '用户管理'
        );

        $pluginSignature = str_replace('_', '', 'user') . '_user';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:user/Configuration/FlexForms/flexform_user.xml');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('user', 'Configuration/TypoScript', '用户管理');

    }
);
