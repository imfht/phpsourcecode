<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Jykj.Filemanage',
            'Filemanage',
            '文件管理'
        );

        $pluginSignature = str_replace('_', '', 'filemanage') . '_filemanage';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:filemanage/Configuration/FlexForms/flexform_filemanage.xml');

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Jykj.Filemanage',
            'Filetypes',
            '文件分类'
        );

        $pluginSignature = str_replace('_', '', 'filemanage') . '_filetypes';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:filemanage/Configuration/FlexForms/flexform_filetypes.xml');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('filemanage', 'Configuration/TypoScript', '文件管理系统');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_filemanage_domain_model_filemanage', 'EXT:filemanage/Resources/Private/Language/locallang_csh_tx_filemanage_domain_model_filemanage.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_filemanage_domain_model_filemanage');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_filemanage_domain_model_filetypes', 'EXT:filemanage/Resources/Private/Language/locallang_csh_tx_filemanage_domain_model_filetypes.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_filemanage_domain_model_filetypes');

    }
);
