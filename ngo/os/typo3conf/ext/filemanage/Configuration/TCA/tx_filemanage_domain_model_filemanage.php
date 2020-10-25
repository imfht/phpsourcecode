<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:filemanage/Resources/Private/Language/locallang_db.xlf:tx_filemanage_domain_model_filemanage',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title,filepath,senddate',
        'iconfile' => 'EXT:filemanage/Resources/Public/Icons/tx_filemanage_domain_model_filemanage.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, filepath, senddate, sort, filetypes',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, filepath, senddate, sort, filetypes, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ]
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_filemanage_domain_model_filemanage',
                'foreign_table_where' => 'AND {#tx_filemanage_domain_model_filemanage}.{#pid}=###CURRENT_PID### AND {#tx_filemanage_domain_model_filemanage}.{#sys_language_uid} IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],

        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:filemanage/Resources/Private/Language/locallang_db.xlf:tx_filemanage_domain_model_filemanage.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'filepath' => [
            'exclude' => true,
            'label' => 'LLL:EXT:filemanage/Resources/Private/Language/locallang_db.xlf:tx_filemanage_domain_model_filemanage.filepath',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'fileimg' => [
            'exclude' => true,
            'label' => 'LLL:EXT:filemanage/Resources/Private/Language/locallang_db.xlf:tx_filemanage_domain_model_filemanage.fileimg',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'senddate' => [
            'exclude' => true,
            'label' => 'LLL:EXT:filemanage/Resources/Private/Language/locallang_db.xlf:tx_filemanage_domain_model_filemanage.senddate',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'sort' => [
            'exclude' => true,
            'label' => 'LLL:EXT:filemanage/Resources/Private/Language/locallang_db.xlf:tx_filemanage_domain_model_filemanage.sort',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'filetypeid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:filemanage/Resources/Private/Language/locallang_db.xlf:tx_filemanage_domain_model_filemanage.filetypeid',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'filetypes' => [
            'exclude' => true,
            'label' => 'LLL:EXT:filemanage/Resources/Private/Language/locallang_db.xlf:tx_filemanage_domain_model_filemanage.filetypes',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_filemanage_domain_model_filetypes',
                'minitems' => 0,
                'maxitems' => 1,
                'appearance' => [
                    'collapseAll' => 0,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
            ],
        ],
    
    ],
];
