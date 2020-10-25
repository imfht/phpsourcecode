<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.Dicts',
            'Area',
            [
                'Area' => 'list, show, new, create, edit, update, delete, interface'
            ],
            // non-cacheable actions
            [
                'Area' => 'list, show, new, create, edit, update, delete, interface'
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.Dicts',
            'Dicttype',
            [
                'Dicttype' => 'list, show, new, create, edit, update, delete, interface'
            ],
            // non-cacheable actions
            [
                'Dicttype' => 'list, show, new, create, edit, update, delete, interface'
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.Dicts',
            'Dictitem',
            [
                'Dictitem' => 'list, show, new, create, edit, update, delete, interface'
            ],
            // non-cacheable actions
            [
                'Dictitem' => 'list, show, new, create, edit, update, delete, interface'
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    area {
                        iconIdentifier = dicts-plugin-area
                        title = LLL:EXT:dicts/Resources/Private/Language/locallang_db.xlf:tx_dicts_area.name
                        description = LLL:EXT:dicts/Resources/Private/Language/locallang_db.xlf:tx_dicts_area.description
                        tt_content_defValues {
                            CType = list
                            list_type = dicts_area
                        }
                    }
                    dicttype {
                        iconIdentifier = dicts-plugin-dicttype
                        title = LLL:EXT:dicts/Resources/Private/Language/locallang_db.xlf:tx_dicts_dicttype.name
                        description = LLL:EXT:dicts/Resources/Private/Language/locallang_db.xlf:tx_dicts_dicttype.description
                        tt_content_defValues {
                            CType = list
                            list_type = dicts_dicttype
                        }
                    }
                    dictitem {
                        iconIdentifier = dicts-plugin-dictitem
                        title = LLL:EXT:dicts/Resources/Private/Language/locallang_db.xlf:tx_dicts_dictitem.name
                        description = LLL:EXT:dicts/Resources/Private/Language/locallang_db.xlf:tx_dicts_dictitem.description
                        tt_content_defValues {
                            CType = list
                            list_type = dicts_dictitem
                        }
                    }
                }
                show = *
            }
       }'
    );
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
			$iconRegistry->registerIcon(
				'dicts-plugin-area',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:dicts/Resources/Public/Icons/user_plugin_area.svg']
			);
		
			$iconRegistry->registerIcon(
				'dicts-plugin-dicttype',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:dicts/Resources/Public/Icons/user_plugin_dicttype.svg']
			);
		
			$iconRegistry->registerIcon(
				'dicts-plugin-dictitem',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:dicts/Resources/Public/Icons/user_plugin_dictitem.svg']
			);
		
    }
);
