<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Jykj.Dicts',
            'Area',
            '字典区域'
        );

        $pluginSignature = str_replace('_', '', 'dicts') . '_area';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:dicts/Configuration/FlexForms/flexform_area.xml');

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Jykj.Dicts',
            'Dicttype',
            '字典大类'
        );

        $pluginSignature = str_replace('_', '', 'dicts') . '_dicttype';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:dicts/Configuration/FlexForms/flexform_dicttype.xml');

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Jykj.Dicts',
            'Dictitem',
            '字典小类'
        );

        $pluginSignature = str_replace('_', '', 'dicts') . '_dictitem';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:dicts/Configuration/FlexForms/flexform_dictitem.xml');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('dicts', 'Configuration/TypoScript', '数据字典');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_dicts_domain_model_area', 'EXT:dicts/Resources/Private/Language/locallang_csh_tx_dicts_domain_model_area.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_dicts_domain_model_area');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_dicts_domain_model_dicttype', 'EXT:dicts/Resources/Private/Language/locallang_csh_tx_dicts_domain_model_dicttype.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_dicts_domain_model_dicttype');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_dicts_domain_model_dictitem', 'EXT:dicts/Resources/Private/Language/locallang_csh_tx_dicts_domain_model_dictitem.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_dicts_domain_model_dictitem');

    }
);
