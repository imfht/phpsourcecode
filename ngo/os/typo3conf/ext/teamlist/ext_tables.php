<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Jykj.Teamlist',
            'Teamwork',
            '团队列表'
        );

        $pluginSignature = str_replace('_', '', 'teamlist') . '_teamwork';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:teamlist/Configuration/FlexForms/flexform_teamwork.xml');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('teamlist', 'Configuration/TypoScript', '团队列表');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_teamlist_domain_model_team', 'EXT:teamlist/Resources/Private/Language/locallang_csh_tx_teamlist_domain_model_team.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_teamlist_domain_model_team');

    }
);
