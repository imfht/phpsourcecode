<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.Donation',
            'Pi1',
            [
                'Pay' => 'list, show, new, create, edit, update, delete, success, search, certificate, multidelete'
            ],
            // non-cacheable actions
            [
                'Pay' => 'list, show, new, create, edit, update, delete, success, search, certificate, multidelete'
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    pi1 {
                        iconIdentifier = donation-plugin-pi1
                        title = LLL:EXT:donation/Resources/Private/Language/locallang_db.xlf:tx_donation_pi1.name
                        description = LLL:EXT:donation/Resources/Private/Language/locallang_db.xlf:tx_donation_pi1.description
                        tt_content_defValues {
                            CType = list
                            list_type = donation_pi1
                        }
                    }
                }
                show = *
            }
       }'
    );
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
			$iconRegistry->registerIcon(
				'donation-plugin-pi1',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:donation/Resources/Public/Icons/user_plugin_pi1.svg']
			);
		
    }
);
