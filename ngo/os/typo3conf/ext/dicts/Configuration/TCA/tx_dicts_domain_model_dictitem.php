<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:dicts/Resources/Private/Language/locallang_db.xlf:tx_dicts_domain_model_dictitem',
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
        'searchFields' => 'name,shortname,code,remarks,image',
        'iconfile' => 'EXT:dicts/Resources/Public/Icons/tx_dicts_domain_model_dictitem.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, shortname, code, remarks, image, sort, spare1, parentuid, dicttype',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, shortname, code, remarks, image, sort, spare1, parentuid, dicttype, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
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
                'foreign_table' => 'tx_dicts_domain_model_dictitem',
                'foreign_table_where' => 'AND {#tx_dicts_domain_model_dictitem}.{#pid}=###CURRENT_PID### AND {#tx_dicts_domain_model_dictitem}.{#sys_language_uid} IN (-1,0)',
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
            'label' => 'LLL:EXT:dicts/Resources/Private/Language/locallang_db.xlf:tx_dicts_domain_model_dictitem.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'shortname' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dicts/Resources/Private/Language/locallang_db.xlf:tx_dicts_domain_model_dictitem.shortname',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'code' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dicts/Resources/Private/Language/locallang_db.xlf:tx_dicts_domain_model_dictitem.code',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'remarks' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dicts/Resources/Private/Language/locallang_db.xlf:tx_dicts_domain_model_dictitem.remarks',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'image' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dicts/Resources/Private/Language/locallang_db.xlf:tx_dicts_domain_model_dictitem.image',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'sort' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dicts/Resources/Private/Language/locallang_db.xlf:tx_dicts_domain_model_dictitem.sort',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'spare1' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dicts/Resources/Private/Language/locallang_db.xlf:tx_dicts_domain_model_dictitem.spare1',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'parentuid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dicts/Resources/Private/Language/locallang_db.xlf:tx_dicts_domain_model_dictitem.parentuid',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_dicts_domain_model_dictitem',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'dicttype' => [
            'exclude' => true,
            'label' => 'LLL:EXT:dicts/Resources/Private/Language/locallang_db.xlf:tx_dicts_domain_model_dictitem.dicttype',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_dicts_domain_model_dicttype',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
    
    ],
];
