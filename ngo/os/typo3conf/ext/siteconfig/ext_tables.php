<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Jykj.Siteconfig',
            'Config',
            '系统配置'
        );

        $pluginSignature = str_replace('_', '', 'siteconfig') . '_config';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:siteconfig/Configuration/FlexForms/flexform_config.xml');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('siteconfig', 'Configuration/TypoScript', '系统配置');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_siteconfig_domain_model_config', 'EXT:siteconfig/Resources/Private/Language/locallang_csh_tx_siteconfig_domain_model_config.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_siteconfig_domain_model_config');

        //pages
        $tempColumns = array(
            'icon' => array ( 
                'exclude' => 1,
                'label' => '菜单图标',
                'config' => array (
                    'type' => 'input',
                    'size' => 5,
                    'eval' => 'trim',
                    'default' => 'fa-dot-circle-o'
                )
            ),
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages',$tempColumns,1);
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages','icon');
        
    }
);
