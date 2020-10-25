<?php
defined('TYPO3_MODE') || die();

/***************
 * Add default RTE configuration
 */
$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['website'] = 'EXT:website/Configuration/RTE/Default.yaml';

/***************
 * PageTS
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $_EXTKEY . '/Configuration/TsConfig/Page/All.tsconfig">');


$GLOBALS['TCA']['sys_file_collection'] = [
    'settings.detail.pageUid123' => [
        'label' => 'detail form123',
        'config' => [
            'type' => 'group',
            'internal_type' => 'db',
            'size' => 1,
            'maxitems' => 1,
            'minitems' => 0,
            'show_thumbs' => 0,
            'suggestOptions' => [
                'default' => [
                    'searchWholePhrase' => 1
                ],
                'pages' => [
                    'searchCondition' => 'doktype = 1'
                ]
            ]
        ],
    ],
];