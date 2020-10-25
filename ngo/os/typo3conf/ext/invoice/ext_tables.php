<?php
defined('TYPO3_MODE') || die('Access denied.');
$composerAutoloadFile = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("phpexcel_service").'Resources/Private/Contributed/PHPExcel.php';
require_once($composerAutoloadFile);
call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Jykj.Invoice',
            'Fpgl',
            '发票管理'
        );

        $pluginSignature = str_replace('_', '', 'invoice') . '_fpgl';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:invoice/Configuration/FlexForms/flexform_fpgl.xml');

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Jykj.Invoice',
            'Channel',
            '捐款渠道'
        );

        $pluginSignature = str_replace('_', '', 'invoice') . '_channel';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:invoice/Configuration/FlexForms/flexform_channel.xml');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('invoice', 'Configuration/TypoScript', '发票管理');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_invoice_domain_model_invoice', 'EXT:invoice/Resources/Private/Language/locallang_csh_tx_invoice_domain_model_invoice.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_invoice_domain_model_invoice');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_invoice_domain_model_channels', 'EXT:invoice/Resources/Private/Language/locallang_csh_tx_invoice_domain_model_channels.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_invoice_domain_model_channels');

    }
);
