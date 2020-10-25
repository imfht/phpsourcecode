<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:teamlist/Resources/Private/Language/locallang_db.xlf:tx_teamlist_domain_model_team',
        'label' => 'name',
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
        'searchFields' => 'name,intro,image,detail,spare1,spare2,spare3',
        'iconfile' => 'EXT:teamlist/Resources/Public/Icons/tx_teamlist_domain_model_team.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, intro, image, detail, spare1, spare2, spare3, orders',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, intro, image, detail, spare1, spare2, spare3, orders, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
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
                'foreign_table' => 'tx_teamlist_domain_model_team',
                'foreign_table_where' => 'AND {#tx_teamlist_domain_model_team}.{#pid}=###CURRENT_PID### AND {#tx_teamlist_domain_model_team}.{#sys_language_uid} IN (-1,0)',
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

        'name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:teamlist/Resources/Private/Language/locallang_db.xlf:tx_teamlist_domain_model_team.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'intro' => [
            'exclude' => true,
            'label' => 'LLL:EXT:teamlist/Resources/Private/Language/locallang_db.xlf:tx_teamlist_domain_model_team.intro',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'image' => [
            'exclude' => true,
            'label' => 'LLL:EXT:teamlist/Resources/Private/Language/locallang_db.xlf:tx_teamlist_domain_model_team.image',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'detail' => [
            'exclude' => true,
            'label' => 'LLL:EXT:teamlist/Resources/Private/Language/locallang_db.xlf:tx_teamlist_domain_model_team.detail',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'spare1' => [
            'exclude' => true,
            'label' => 'LLL:EXT:teamlist/Resources/Private/Language/locallang_db.xlf:tx_teamlist_domain_model_team.spare1',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'spare2' => [
            'exclude' => true,
            'label' => 'LLL:EXT:teamlist/Resources/Private/Language/locallang_db.xlf:tx_teamlist_domain_model_team.spare2',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'spare3' => [
            'exclude' => true,
            'label' => 'LLL:EXT:teamlist/Resources/Private/Language/locallang_db.xlf:tx_teamlist_domain_model_team.spare3',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'orders' => [
            'exclude' => true,
            'label' => 'LLL:EXT:teamlist/Resources/Private/Language/locallang_db.xlf:tx_teamlist_domain_model_team.orders',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
    
    ],
];
