<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Jykj.Donation',
            'Pi1',
            '捐赠插件'
        );

        $pluginSignature = str_replace('_', '', 'donation') . '_pi1';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:donation/Configuration/FlexForms/flexform_pi1.xml');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('donation', 'Configuration/TypoScript', 'Payment Module');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_donation_domain_model_pay', 'EXT:donation/Resources/Private/Language/locallang_csh_tx_donation_domain_model_pay.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_donation_domain_model_pay');

    }
);
