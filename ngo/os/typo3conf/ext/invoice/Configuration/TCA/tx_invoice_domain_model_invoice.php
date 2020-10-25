<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_domain_model_invoice',
        'label' => 'money',
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
        'searchFields' => 'header,address,postcode,people,telphone,mail,spare1,spare2,spare3,spare4,spare5',
        'iconfile' => 'EXT:invoice/Resources/Public/Icons/tx_invoice_domain_model_invoice.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, money, header, address, postcode, people, telphone, mail, donatetime, spare1, spare2, spare3, spare4, spare5, channelid',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, money, header, address, postcode, people, telphone, mail, donatetime, spare1, spare2, spare3, spare4, spare5, channelid, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
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
                'foreign_table' => 'tx_invoice_domain_model_invoice',
                'foreign_table_where' => 'AND {#tx_invoice_domain_model_invoice}.{#pid}=###CURRENT_PID### AND {#tx_invoice_domain_model_invoice}.{#sys_language_uid} IN (-1,0)',
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

        'money' => [
            'exclude' => true,
            'label' => 'LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_domain_model_invoice.money',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2'
            ]
        ],
        'header' => [
            'exclude' => true,
            'label' => 'LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_domain_model_invoice.header',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'address' => [
            'exclude' => true,
            'label' => 'LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_domain_model_invoice.address',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'postcode' => [
            'exclude' => true,
            'label' => 'LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_domain_model_invoice.postcode',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'people' => [
            'exclude' => true,
            'label' => 'LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_domain_model_invoice.people',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'telphone' => [
            'exclude' => true,
            'label' => 'LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_domain_model_invoice.telphone',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'mail' => [
            'exclude' => true,
            'label' => 'LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_domain_model_invoice.mail',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'donatetime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_domain_model_invoice.donatetime',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'spare1' => [
            'exclude' => true,
            'label' => 'LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_domain_model_invoice.spare1',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'spare2' => [
            'exclude' => true,
            'label' => 'LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_domain_model_invoice.spare2',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'spare3' => [
            'exclude' => true,
            'label' => 'LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_domain_model_invoice.spare3',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'spare4' => [
            'exclude' => true,
            'label' => 'LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_domain_model_invoice.spare4',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'spare5' => [
            'exclude' => true,
            'label' => 'LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_domain_model_invoice.spare5',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'channelid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:invoice/Resources/Private/Language/locallang_db.xlf:tx_invoice_domain_model_invoice.channelid',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_invoice_domain_model_channels',
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
