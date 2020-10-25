<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.Timeline',
            'Timeline',
            [
                'Timeline' => 'list, show, new, create, edit, update, delete, multedelete, qtlist, spareajax'
            ],
            // non-cacheable actions
            [
                'Timeline' => 'list, show, new, create, edit, update, delete, multedelete, spareajax'
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    timeline {
                        iconIdentifier = timeline-plugin-timeline
                        title = LLL:EXT:timeline/Resources/Private/Language/locallang_db.xlf:tx_timeline_timeline.name
                        description = LLL:EXT:timeline/Resources/Private/Language/locallang_db.xlf:tx_timeline_timeline.description
                        tt_content_defValues {
                            CType = list
                            list_type = timeline_timeline
                        }
                    }
                }
                show = *
            }
       }'
    );
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
			$iconRegistry->registerIcon(
				'timeline-plugin-timeline',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:timeline/Resources/Public/Icons/user_plugin_timeline.svg']
			);
		
    }
);
