<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.User',
            'User',
            [
                'User' => 'list,new,create,show,edit,update,delete,password,updatepwd,orginfo,updateorg'
            ],
            // non-cacheable actions
            [
                'User' => 'list,new,create,show,edit,update,delete,password,updatepwd,orginfo,updateorg'
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    user {
                        iconIdentifier = user-plugin-user
                        title = LLL:EXT:user/Resources/Private/Language/locallang_db.xlf:tx_user_user.name
                        description = LLL:EXT:user/Resources/Private/Language/locallang_db.xlf:tx_user_user.description
                        tt_content_defValues {
                            CType = list
                            list_type = user_user
                        }
                    }
                }
                show = *
            }
       }'
    );
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
			$iconRegistry->registerIcon(
				'user-plugin-user',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:user/Resources/Public/Icons/user_plugin_user.svg']
			);
		
    }
);
