<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Jykj.CaseTab',
            'Casetype',
            '应用案例_案例类型管理'
        );

        $pluginSignature = str_replace('_', '', 'case_tab') . '_casetype';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:case_tab/Configuration/FlexForms/flexform_casetype.xml');

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Jykj.CaseTab',
            'Casetab',
            '应用案例_案例管理'
        );

        $pluginSignature = str_replace('_', '', 'case_tab') . '_casetab';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:case_tab/Configuration/FlexForms/flexform_casetab.xml');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('case_tab', 'Configuration/TypoScript', '应用案例');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_casetab_domain_model_casetab', 'EXT:case_tab/Resources/Private/Language/locallang_csh_tx_casetab_domain_model_casetab.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_casetab_domain_model_casetab');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_casetab_domain_model_casetype', 'EXT:case_tab/Resources/Private/Language/locallang_csh_tx_casetab_domain_model_casetype.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_casetab_domain_model_casetype');

    }
);
