<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.Echarts',
            'Pi1',
            [
                'Echarts' => 'list, show, new, create, edit, update, delete, multidelete, chart, chartUpdate, bar, line, pie, funnel'
            ],
            // non-cacheable actions
            [
                'Echarts' => 'list, show, new, create, edit, update, delete, multidelete, chart, chartUpdate, bar, line, pie, funnel'
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.Echarts',
            'Bar',
            [
                'Echarts' => 'bar'
            ],
            // non-cacheable actions
            [
                'Echarts' => 'bar'
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.Echarts',
            'Line',
            [
                'Echarts' => 'line'
            ],
            // non-cacheable actions
            [
                'Echarts' => 'line'
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.Echarts',
            'Pie',
            [
                'Echarts' => 'pie'
            ],
            // non-cacheable actions
            [
                'Echarts' => 'pie'
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Jykj.Echarts',
            'Funnel',
            [
                'Echarts' => 'funnel'
            ],
            // non-cacheable actions
            [
                'Echarts' => 'funnel'
            ]
        );

        // add content element to insert tables in content element wizard
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            'mod {
                wizards.newContentElement.wizardItems.plugins {
                    elements {
                        pi1 {
                            iconIdentifier = echarts-plugin-pi1
                            title = LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_pi1.name
                            description = LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_pi1.description
                            tt_content_defValues {
                                CType = list
                                list_type = echarts_pi1
                            }
                        }
                    }
                }
                wizards.newContentElement.wizardItems.special {
                    elements {
                        bar {
                            iconIdentifier = echarts-plugin-bar
                            title = LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_bar.name
                            description = LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_bar.description
                            tt_content_defValues {
                                CType = list
                                list_type = echarts_bar
                            }
                        }
                        line {
                            iconIdentifier = echarts-plugin-line
                            title = LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_line.name
                            description = LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_line.description
                            tt_content_defValues {
                                CType = list
                                list_type = echarts_line
                            }
                        }
                        pie {
                            iconIdentifier = echarts-plugin-pie
                            title = LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_pie.name
                            description = LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_pie.description
                            tt_content_defValues {
                                CType = list
                                list_type = echarts_pie
                            }
                        }
                        funnel {
                            iconIdentifier = echarts-plugin-funnel
                            title = LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_funnel.name
                            description = LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_funnel.description
                            tt_content_defValues {
                                CType = list
                                list_type = echarts_funnel
                            }
                        }
                    }
                    show = *
                }
        }'
        );

		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
        $iconRegistry->registerIcon('echarts-plugin-pi1',\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,['source' => 'EXT:echarts/Resources/Public/Icons/user_plugin_pi1.svg']);
    
        $iconRegistry->registerIcon('echarts-plugin-bar',\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,['source' => 'EXT:echarts/Resources/Public/Icons/user_plugin_bar.svg']);
    
        $iconRegistry->registerIcon('echarts-plugin-line',\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,['source' => 'EXT:echarts/Resources/Public/Icons/user_plugin_line.svg']);
    
        $iconRegistry->registerIcon('echarts-plugin-pie',\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,['source' => 'EXT:echarts/Resources/Public/Icons/user_plugin_pie.svg']);
    
        $iconRegistry->registerIcon('echarts-plugin-funnel',\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,['source' => 'EXT:echarts/Resources/Public/Icons/user_plugin_funnel.svg']);
		
    }
);
