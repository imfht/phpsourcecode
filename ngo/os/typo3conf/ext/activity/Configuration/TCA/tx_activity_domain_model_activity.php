<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity',
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
        'searchFields' => 'name,address,tag,pictures,introduce,contents,qrcode,results',
        'iconfile' => 'EXT:activity/Resources/Public/Icons/tx_activity_domain_model_activity.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, province, city, area, address, trad, types, tag, people, pictures, sttime, overtime, introduce, contents, qrcode, sendstat, mode, money, ckstat, results, checkuser, senduser',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, province, city, area, address, trad, types, tag, people, pictures, sttime, overtime, introduce, contents, qrcode, sendstat, mode, money, ckstat, results, checkuser, senduser, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
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
                'foreign_table' => 'tx_activity_domain_model_activity',
                'foreign_table_where' => 'AND {#tx_activity_domain_model_activity}.{#pid}=###CURRENT_PID### AND {#tx_activity_domain_model_activity}.{#sys_language_uid} IN (-1,0)',
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
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'province' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.province',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'city' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.city',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'area' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.area',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'address' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.address',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'trad' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.trad',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'types' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.types',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'tag' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.tag',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'people' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.people',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'pictures' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.pictures',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'sttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.sttime',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'overtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.overtime',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'introduce' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.introduce',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'contents' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.contents',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'qrcode' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.qrcode',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'sendstat' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.sendstat',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'mode' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.mode',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'money' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.money',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2'
            ]
        ],
        'ckstat' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.ckstat',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'results' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.results',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'checkuser' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.checkuser',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'senduser' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.senduser',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'way' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.way',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'week' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.week',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'hour' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.hour',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'deltag' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_activity.deltag',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
    
    ],
];
