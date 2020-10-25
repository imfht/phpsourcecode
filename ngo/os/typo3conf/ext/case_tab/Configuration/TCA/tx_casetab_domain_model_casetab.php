<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_casetab_domain_model_casetab',
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
        'searchFields' => 'title,content,image,spare1,spare2,spare3,spare4,spare5,spare6,product,labels',
        'iconfile' => 'EXT:case_tab/Resources/Public/Icons/tx_casetab_domain_model_casetab.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, content, image, datetime, spare1, spare2, spare3, spare4, spare5, spare6, product, labels, hits, casetype, industry',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, content, image, datetime, spare1, spare2, spare3, spare4, spare5, spare6, product, labels, hits, casetype, industry, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
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
                'foreign_table' => 'tx_casetab_domain_model_casetab',
                'foreign_table_where' => 'AND {#tx_casetab_domain_model_casetab}.{#pid}=###CURRENT_PID### AND {#tx_casetab_domain_model_casetab}.{#sys_language_uid} IN (-1,0)',
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
            'label' => 'LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_casetab_domain_model_casetab.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'content' => [
            'exclude' => true,
            'label' => 'LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_casetab_domain_model_casetab.content',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'image' => [
            'exclude' => true,
            'label' => 'LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_casetab_domain_model_casetab.image',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'datetime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_casetab_domain_model_casetab.datetime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 10,
                'eval' => 'datetime',
                'default' => time()
            ],
        ],
        'spare1' => [
            'exclude' => true,
            'label' => 'LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_casetab_domain_model_casetab.spare1',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'spare2' => [
            'exclude' => true,
            'label' => 'LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_casetab_domain_model_casetab.spare2',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'spare3' => [
            'exclude' => true,
            'label' => 'LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_casetab_domain_model_casetab.spare3',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'spare4' => [
            'exclude' => true,
            'label' => 'LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_casetab_domain_model_casetab.spare4',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'spare5' => [
            'exclude' => true,
            'label' => 'LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_casetab_domain_model_casetab.spare5',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'spare6' => [
            'exclude' => true,
            'label' => 'LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_casetab_domain_model_casetab.spare6',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'product' => [
            'exclude' => true,
            'label' => 'LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_casetab_domain_model_casetab.product',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'labels' => [
            'exclude' => true,
            'label' => 'LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_casetab_domain_model_casetab.labels',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'hits' => [
            'exclude' => true,
            'label' => 'LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_casetab_domain_model_casetab.hits',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'casetype' => [
            'exclude' => true,
            'label' => 'LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_casetab_domain_model_casetab.casetype',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_casetab_domain_model_casetype',
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
        'industry' => [
            'exclude' => true,
            'label' => 'LLL:EXT:case_tab/Resources/Private/Language/locallang_db.xlf:tx_casetab_domain_model_casetab.industry',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_dicts_domain_model_dictitem',
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
