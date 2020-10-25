<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.Teamlist',
            'Teamwork',
            [
                'Team' => 'list, show, new, create, edit, update, delete'
            ],
            // non-cacheable actions
            [
                'Team' => 'list, show, new, create, edit, update, delete'
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    teamwork {
                        iconIdentifier = teamlist-plugin-teamwork
                        title = LLL:EXT:teamlist/Resources/Private/Language/locallang_db.xlf:tx_teamlist_teamwork.name
                        description = LLL:EXT:teamlist/Resources/Private/Language/locallang_db.xlf:tx_teamlist_teamwork.description
                        tt_content_defValues {
                            CType = list
                            list_type = teamlist_teamwork
                        }
                    }
                }
                show = *
            }
       }'
    );
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
			$iconRegistry->registerIcon(
				'teamlist-plugin-teamwork',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:teamlist/Resources/Public/Icons/user_plugin_teamwork.svg']
			);
		
    }
);
