<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer',
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
        'searchFields' => 'name,birthday,email,telephone,qqcode,weibo,descritpion,skill,duty,org,ranks,wechat,idcard,emcontact,emtelephone',
        'iconfile' => 'EXT:activity/Resources/Public/Icons/tx_activity_domain_model_volunteer.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, birthday, email, telephone, qqcode, weibo, descritpion, isexperience, skill, duty, org, ranks, wechat, idcard, emcontact, emtelephone, sex, province, community, identity',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, birthday, email, telephone, qqcode, weibo, descritpion, isexperience, skill, duty, org, ranks, wechat, idcard, emcontact, emtelephone, sex, province, community, identity, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
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
                'foreign_table' => 'tx_activity_domain_model_volunteer',
                'foreign_table_where' => 'AND {#tx_activity_domain_model_volunteer}.{#pid}=###CURRENT_PID### AND {#tx_activity_domain_model_volunteer}.{#sys_language_uid} IN (-1,0)',
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
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'birthday' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.birthday',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'email' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.email',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'telephone' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.telephone',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'qqcode' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.qqcode',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'weibo' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.weibo',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'descritpion' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.descritpion',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'isexperience' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.isexperience',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'skill' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.skill',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'duty' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.duty',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'org' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.org',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'ranks' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.ranks',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'wechat' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.wechat',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'idcard' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.idcard',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'emcontact' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.emcontact',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'emtelephone' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.emtelephone',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'sex' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.sex',
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
        'province' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.province',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_dicts_domain_model_area',
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
        'community' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.community',
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
        'identity' => [
            'exclude' => true,
            'label' => 'LLL:EXT:activity/Resources/Private/Language/locallang_db.xlf:tx_activity_domain_model_volunteer.identity',
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
