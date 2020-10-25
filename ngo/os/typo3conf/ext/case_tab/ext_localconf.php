<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.CaseTab',
            'Casetype',
            [
                'Casetype' => 'list, show, new, create, edit, update, delete'
            ],
            // non-cacheable actions
            [
                'Casetype' => 'list, show, new, create, edit, update, delete'
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.CaseTab',
            'Casetab',
            [
                'Casetab' => 'list, show, new, create, edit, update, delete, sylist, nylist, nyajax'
            ],
            // non-cacheable actions
            [
                'Casetab' => 'list, show, new, create, edit, update, delete, sylist, nylist, nyajax'
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    casetype {
                        iconIdentifier = case_tab-plugin-casetype
                        title = LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_case_tab_casetype.name
                        description = LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_case_tab_casetype.description
                        tt_content_defValues {
                            CType = list
                            list_type = casetab_casetype
                        }
                    }
                    casetab {
                        iconIdentifier = case_tab-plugin-casetab
                        title = LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_case_tab_casetab.name
                        description = LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_case_tab_casetab.description
                        tt_content_defValues {
                            CType = list
                            list_type = casetab_casetab
                        }
                    }
                }
                show = *
            }
       }'
    );
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
			$iconRegistry->registerIcon(
				'case_tab-plugin-casetype',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:case_tab/Resources/Public/Icons/user_plugin_casetype.svg']
			);
		
			$iconRegistry->registerIcon(
				'case_tab-plugin-casetab',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:case_tab/Resources/Public/Icons/user_plugin_casetab.svg']
			);
		
    }
);
