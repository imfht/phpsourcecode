<?php
defined('TYPO3_MODE') || die();

$additionalColumns = [
    'datetime' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:date_formlabel',
        'config' => [
            'type' => 'input',
            'size' => 12,
            'max' => 20,
            'eval' => 'datetime',
            'default' => 0,
        ]
    ],
    'sorting' => [
        'label' => 'sorting',
        'config' => [
            'type' => 'passthrough'
        ]
    ],
    'webdescription' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:fs_media_gallery/Resources/Private/Language/locallang_db.xlf:tx_fsmediagallery_domain_model_mediaalbum.webdescription',
        'config' => [
            'type' => 'text',
            'cols' => 40,
            'rows' => 5,
            'eval' => 'trim',
            'enableRichtext' => true,
            'fieldControl' => [
                'fullScreenRichtext' => [
                    'disabled' => false,
                ],
            ],
        ],
        'defaultExtras' => 'richtext[]',
    ],
    'parentalbum' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:fs_media_gallery/Resources/Private/Language/locallang_db.xlf:tx_fsmediagallery_domain_model_mediaalbum.parentalbum',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectTree',
            'foreign_table' => 'sys_file_collection',
            'foreign_table_where' => ' AND (sys_file_collection.sys_language_uid = 0 OR sys_file_collection.l10n_parent = 0) AND sys_file_collection.pid = ###CURRENT_PID### AND sys_file_collection.uid != ###THIS_UID### ORDER BY sys_file_collection.sorting ASC, sys_file_collection.crdate DESC',
            'subType' => 'db',
            'treeConfig' => [
                'parentField' => 'parentalbum',
                'appearance' => [
                    'showHeader' => true,
                    'maxLevels' => 99,
                    'width' => 650,
                ],
            ],
            'size' => 10,
            'autoSizeMax' => 20,
            'minitems' => 0,
            'maxitems' => 1,
            'default' => 0,
        ]
    ],
    'main_asset' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:fs_media_gallery/Resources/Private/Language/locallang_db.xlf:tx_fsmediagallery_domain_model_mediaalbum.main_asset',
        'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
            'images',
            [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:fs_media_gallery/Resources/Private/Language/locallang_db.xlf:tx_fsmediagallery_domain_model_mediaalbum.main_asset.add'
                ],
                'maxitems' => 1,
            ],
            $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
        )
    ]
];

if (version_compare(TYPO3_branch, '9.5', '>=')) {
    $additionalColumns['slug'] = [
        'exclude' => true,
        'label' => 'LLL:EXT:fs_media_gallery/Resources/Private/Language/locallang_db.xlf:tx_fsmediagallery_domain_model_mediaalbum.slug',
        'config' => [
            'type' => 'slug',
            'generatorOptions' => [
                'fields' => ['title'],
                'fieldSeparator' => '/',
                'prefixParentPageSlug' => false,
            ],
            'fallbackCharacter' => '-',
            'eval' => 'uniqueInPid',
        ],
    ];
}

foreach ($GLOBALS['TCA']['sys_file_collection']['types'] as $type => $tmp) {
    $GLOBALS['TCA']['sys_file_collection']['types'][$type]['showitem'] .= ',--div--;LLL:EXT:fs_media_gallery/Resources/Private/Language/locallang_db.xlf:tx_fsmediagallery_domain_model_mediaalbum';
    // try to add field datetime before type (after title)
    if ($replacedTca = preg_replace('/(\s*)type(\s*)(;|,)/', 'datetime,type$3',
        $GLOBALS['TCA']['sys_file_collection']['types'][$type]['showitem'])
    ) {
        $GLOBALS['TCA']['sys_file_collection']['types'][$type]['showitem'] = $replacedTca;
    } else {
        $GLOBALS['TCA']['sys_file_collection']['types'][$type]['showitem'] .= ',datetime';
    }
    $GLOBALS['TCA']['sys_file_collection']['types'][$type]['showitem'] .= ',slug,parentalbum,main_asset,webdescription';
}

// enable manual sorting
$GLOBALS['TCA']['sys_file_collection']['ctrl']['sortby'] = 'sorting';
$GLOBALS['TCA']['sys_file_collection']['ctrl']['default_sortby'] = 'ORDER BY sorting ASC, crdate DESC';

// enable main asset preview in list module
$GLOBALS['TCA']['sys_file_collection']['ctrl']['thumbnail'] = 'main_asset';


\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
    $GLOBALS['TCA']['sys_file_collection']['columns'],
    $additionalColumns
);
