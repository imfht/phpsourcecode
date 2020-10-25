<?php
defined('TYPO3_MODE') || die('Access denied.');
$composerAutoloadFile = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("phpexcel_service").'Resources/Private/Contributed/PHPExcel.php';
require_once($composerAutoloadFile);
call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.Invoice',
            'Fpgl',
            [
                'Invoice' => 'list, show, new, create, edit, update, delete, success'
            ],
            // non-cacheable actions
            [
                'Invoice' => 'list, show, new, create, edit, update, delete, success'
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.Invoice',
            'Channel',
            [
                'Channels' => 'list, show, new, create, edit, update, delete'
            ],
            // non-cacheable actions
            [
                'Channels' => 'list, show, new, create, edit, update, delete'
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    fpgl {
                        iconIdentifier = invoice-plugin-fpgl
                        title = LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_fpgl.name
                        description = LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_fpgl.description
                        tt_content_defValues {
                            CType = list
                            list_type = invoice_fpgl
                        }
                    }
                    channel {
                        iconIdentifier = invoice-plugin-channel
                        title = LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_channel.name
                        description = LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_channel.description
                        tt_content_defValues {
                            CType = list
                            list_type = invoice_channel
                        }
                    }
                }
                show = *
            }
       }'
    );
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
			$iconRegistry->registerIcon(
				'invoice-plugin-fpgl',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:invoice/Resources/Public/Icons/user_plugin_fpgl.svg']
			);
		
			$iconRegistry->registerIcon(
				'invoice-plugin-channel',
				\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
				['source' => 'EXT:invoice/Resources/Public/Icons/user_plugin_channel.svg']
			);
		
    }
);
