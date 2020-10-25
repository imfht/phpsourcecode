<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.Siteconfig',
            'Config',
            [
                'Config' => 'list,update'
            ],
            // non-cacheable actions
            [
                'Config' => 'list,update'
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    config {
                        iconIdentifier = siteconfig-plugin-config
                        title = LLL:EXT:siteconfig/Resources/Private/Language/locallang_db.xlf:tx_siteconfig_config.name
                        description = LLL:EXT:siteconfig/Resources/Private/Language/locallang_db.xlf:tx_siteconfig_config.description
                        tt_content_defValues {
                            CType = list
                            list_type = siteconfig_config
                        }
                    }
                }
                show = *
            }
       }'
    );
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
			$iconRegistry->registerIcon(
				'siteconfig-plugin-config',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:siteconfig/Resources/Public/Icons/user_plugin_config.svg']
			);
		
    }
);
