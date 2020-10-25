<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:donation/Resources/Private/Language/locallang_db.xlf:tx_donation_domain_model_pay',
        'label' => 'comment',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
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
        'searchFields' => 'comment,title,name,email,telephone,module,url,ordernumber,payment,certnumber',
        'iconfile' => 'EXT:donation/Resources/Public/Icons/tx_donation_domain_model_pay.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, comment, title, money, name, email, telephone, module, datauid, url, ordernumber, payment, certnumber, spreadshareuserid',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, comment, title, money, name, email, telephone, module, datauid, url, ordernumber, payment, certnumber, spreadshareuserid, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'columns' => [
        'crdate' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.creationDate',
            'config' => array(
                'type' => 'input',
                'size' => 5,
                'eval' => 'date',
                'checkbox' => 1,
                'default' => time()
            ),
        ),
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
                'foreign_table' => 'tx_donation_domain_model_pay',
                'foreign_table_where' => 'AND {#tx_donation_domain_model_pay}.{#pid}=###CURRENT_PID### AND {#tx_donation_domain_model_pay}.{#sys_language_uid} IN (-1,0)',
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

        'comment' => [
            'exclude' => true,
            'label' => 'LLL:EXT:donation/Resources/Private/Language/locallang_db.xlf:tx_donation_domain_model_pay.comment',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:donation/Resources/Private/Language/locallang_db.xlf:tx_donation_domain_model_pay.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'money' => [
            'exclude' => true,
            'label' => 'LLL:EXT:donation/Resources/Private/Language/locallang_db.xlf:tx_donation_domain_model_pay.money',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2'
            ]
        ],
        'name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:donation/Resources/Private/Language/locallang_db.xlf:tx_donation_domain_model_pay.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'email' => [
            'exclude' => true,
            'label' => 'LLL:EXT:donation/Resources/Private/Language/locallang_db.xlf:tx_donation_domain_model_pay.email',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'telephone' => [
            'exclude' => true,
            'label' => 'LLL:EXT:donation/Resources/Private/Language/locallang_db.xlf:tx_donation_domain_model_pay.telephone',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'module' => [
            'exclude' => true,
            'label' => 'LLL:EXT:donation/Resources/Private/Language/locallang_db.xlf:tx_donation_domain_model_pay.module',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'datauid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:donation/Resources/Private/Language/locallang_db.xlf:tx_donation_domain_model_pay.datauid',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'url' => [
            'exclude' => true,
            'label' => 'LLL:EXT:donation/Resources/Private/Language/locallang_db.xlf:tx_donation_domain_model_pay.url',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'ordernumber' => [
            'exclude' => true,
            'label' => 'LLL:EXT:donation/Resources/Private/Language/locallang_db.xlf:tx_donation_domain_model_pay.ordernumber',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'payment' => [
            'exclude' => true,
            'label' => 'LLL:EXT:donation/Resources/Private/Language/locallang_db.xlf:tx_donation_domain_model_pay.payment',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'certnumber' => [
            'exclude' => true,
            'label' => 'LLL:EXT:donation/Resources/Private/Language/locallang_db.xlf:tx_donation_domain_model_pay.certnumber',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'spreadshareuserid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:donation/Resources/Private/Language/locallang_db.xlf:tx_donation_domain_model_pay.spreadshareuserid',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
    
    ],
];
