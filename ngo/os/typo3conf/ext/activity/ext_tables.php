<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Jykj.Activity',
            'Activity',
            '志愿者活动'
        );

        $pluginSignature = str_replace('_', '', 'activity') . '_activity';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:activity/Configuration/FlexForms/flexform_activity.xml');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('activity', 'Configuration/TypoScript', '志愿者活动');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_activity_domain_model_activity', 'EXT:activity/Resources/Private/Language/locallang_csh_tx_activity_domain_model_activity.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_activity_domain_model_activity');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_activity_domain_model_signup', 'EXT:activity/Resources/Private/Language/locallang_csh_tx_activity_domain_model_signup.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_activity_domain_model_signup');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_activity_domain_model_volunteer', 'EXT:activity/Resources/Private/Language/locallang_csh_tx_activity_domain_model_volunteer.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_activity_domain_model_volunteer');

    }
);
