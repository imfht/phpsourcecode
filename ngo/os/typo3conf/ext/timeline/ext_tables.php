<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Jykj.Timeline',
            'Timeline',
            '大事记'
        );

        $pluginSignature = str_replace('_', '', 'timeline') . '_timeline';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:timeline/Configuration/FlexForms/flexform_timeline.xml');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('timeline', 'Configuration/TypoScript', 'Timeline');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_timeline_domain_model_timeline', 'EXT:timeline/Resources/Private/Language/locallang_csh_tx_timeline_domain_model_timeline.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_timeline_domain_model_timeline');

    }
);
