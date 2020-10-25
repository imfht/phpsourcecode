<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.Filemanage',
            'Filemanage',
            [
                'Filemanage' => 'list, show, new, create, edit, update, delete, qtlist, download, sylist'
            ],
            // non-cacheable actions
            [
                'Filemanage' => 'list, show, new, create, edit, update, delete, qtlist, download, sylist'
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.Filemanage',
            'Filetypes',
            [
                'Filetype' => 'list, show, new, create, edit, update, delete'
            ],
            // non-cacheable actions
            [
                'Filetype' => 'list, show, new, create, edit, update, delete'
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    filemanage {
                        iconIdentifier = filemanage-plugin-filemanage
                        title = LLL:EXT:filemanage/Resources/Private/Language/locallang_db.xlf:tx_filemanage_filemanage.name
                        description = LLL:EXT:filemanage/Resources/Private/Language/locallang_db.xlf:tx_filemanage_filemanage.description
                        tt_content_defValues {
                            CType = list
                            list_type = filemanage_filemanage
                        }
                    }
                    filetypes {
                        iconIdentifier = filemanage-plugin-filetypes
                        title = LLL:EXT:filemanage/Resources/Private/Language/locallang_db.xlf:tx_filemanage_filetypes.name
                        description = LLL:EXT:filemanage/Resources/Private/Language/locallang_db.xlf:tx_filemanage_filetypes.description
                        tt_content_defValues {
                            CType = list
                            list_type = filemanage_filetypes
                        }
                    }
                }
                show = *
            }
       }'
    );
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
			$iconRegistry->registerIcon(
				'filemanage-plugin-filemanage',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:filemanage/Resources/Public/Icons/user_plugin_filemanage.svg']
			);
		
			$iconRegistry->registerIcon(
				'filemanage-plugin-filetypes',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:filemanage/Resources/Public/Icons/user_plugin_filetypes.svg']
			);
		
    }
);
