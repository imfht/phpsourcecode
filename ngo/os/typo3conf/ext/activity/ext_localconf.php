<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.Activity',
            'Activity',
            [
                'Activity' => 'list, show, new, create, edit, update, delete, interface, download, qtlist, qtshow, send, ajax,multidelete',
                'Signup' => 'list, show, new, create, edit, update, delete, iinterface, statistics, mylist, signin, checkin, success,multidelete',
                'Volunteer' => 'list, show, new, create, edit, update, delete, ajax, success,multidelete'
            ],
            // non-cacheable actions
            [
                'Activity' => 'list, show, new, create, edit, update, delete, interface, download, qtlist, qtshow, send, ajax,multidelete',
                'Signup' => 'list, show, new, create, edit, update, delete, iinterface, statistics, mylist, signin, checkin, success,multidelete',
                'Volunteer' => 'list, show, new, create, edit, update, delete, ajax, success,multidelete'
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    activity {
                        iconIdentifier = activity-plugin-activity
                        title = LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_activity.name
                        description = LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_activity.description
                        tt_content_defValues {
                            CType = list
                            list_type = activity_activity
                        }
                    }
                }
                show = *
            }
       }'
    );
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
			$iconRegistry->registerIcon(
				'activity-plugin-activity',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:activity/Resources/Public/Icons/user_plugin_activity.svg']
			);
		
    }
);
