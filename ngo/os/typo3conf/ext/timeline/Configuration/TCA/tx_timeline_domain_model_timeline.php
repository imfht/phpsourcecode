<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:timeline/Resources/Private/Language/locallang_db.xlf:tx_timeline_domain_model_timeline',
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
        'searchFields' => 'title,bodytext,spare1,spare2,spare3',
        'iconfile' => 'EXT:timeline/Resources/Public/Icons/tx_timeline_domain_model_timeline.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, eventdate, bodytext, spare1, spare2, spare3',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, eventdate, bodytext, spare1, spare2, spare3, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
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
                'foreign_table' => 'tx_timeline_domain_model_timeline',
                'foreign_table_where' => 'AND {#tx_timeline_domain_model_timeline}.{#pid}=###CURRENT_PID### AND {#tx_timeline_domain_model_timeline}.{#sys_language_uid} IN (-1,0)',
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
            'label' => 'LLL:EXT:timeline/Resources/Private/Language/locallang_db.xlf:tx_timeline_domain_model_timeline.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'eventdate' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timeline/Resources/Private/Language/locallang_db.xlf:tx_timeline_domain_model_timeline.eventdate',
            'config' => [
                'dbType' => 'datetime',
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 12,
                'eval' => 'datetime',
                'default' => null,
            ],
        ],
        'bodytext' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timeline/Resources/Private/Language/locallang_db.xlf:tx_timeline_domain_model_timeline.bodytext',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'spare1' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timeline/Resources/Private/Language/locallang_db.xlf:tx_timeline_domain_model_timeline.spare1',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'spare2' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timeline/Resources/Private/Language/locallang_db.xlf:tx_timeline_domain_model_timeline.spare2',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'spare3' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timeline/Resources/Private/Language/locallang_db.xlf:tx_timeline_domain_model_timeline.spare3',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'crdate' => [
            'exclude' => true,
            'label' => 'crdate',
            'config' => [
                    'type' => 'input',
                    'size' => 20,
                    'eval' => 'datetime'
            ],
        ],
    
    ],
];
