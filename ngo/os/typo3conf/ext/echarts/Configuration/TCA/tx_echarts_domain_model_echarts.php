<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_domain_model_echarts',
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
        'searchFields' => 'title,subtitle,theme,tooltip,toolbox,color,textstyle,code,datas,width,height,alignment',
        'iconfile' => 'EXT:echarts/Resources/Public/Icons/tx_echarts_domain_model_echarts.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, echart, title, titlepos, subtitle, sublink, theme, tooltip, toolbox, color, textstyle, code, datas,width,height,alignment',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, echart, title, titlepos, subtitle, sublink, theme, tooltip, toolbox, color, textstyle, code, datas,width,height,alignment, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
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
                'foreign_table' => 'tx_echarts_domain_model_echarts',
                'foreign_table_where' => 'AND {#tx_echarts_domain_model_echarts}.{#pid}=###CURRENT_PID### AND {#tx_echarts_domain_model_echarts}.{#sys_language_uid} IN (-1,0)',
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

        'echart' => [
            'exclude' => true,
            'label' => 'LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_domain_model_echarts.echart',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_domain_model_echarts.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'titlepos' => [
            'exclude' => true,
            'label' => 'LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_domain_model_echarts.titlepos',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'subtitle' => [
            'exclude' => true,
            'label' => 'LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_domain_model_echarts.subtitle',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'sublink' => [
            'exclude' => true,
            'label' => 'LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_domain_model_echarts.sublink',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'author' => [
            'exclude' => true,
            'label' => 'LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_domain_model_echarts.author',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'theme' => [
            'exclude' => true,
            'label' => 'LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_domain_model_echarts.theme',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'tooltip' => [
            'exclude' => true,
            'label' => 'LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_domain_model_echarts.tooltip',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'toolbox' => [
            'exclude' => true,
            'label' => 'LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_domain_model_echarts.toolbox',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'color' => [
            'exclude' => true,
            'label' => 'LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_domain_model_echarts.color',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'textstyle' => [
            'exclude' => true,
            'label' => 'LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_domain_model_echarts.textstyle',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'code' => [
            'exclude' => true,
            'label' => 'LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_domain_model_echarts.code',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'datas' => [
            'exclude' => true,
            'label' => 'LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_domain_model_echarts.datas',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'width' => [
            'exclude' => true,
            'label' => 'LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_domain_model_echarts.width',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'height' => [
            'exclude' => true,
            'label' => 'LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_domain_model_echarts.height',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'alignment' => [
            'exclude' => true,
            'label' => 'LLL:EXT:echarts/Resources/Private/Language/locallang_db.xlf:tx_echarts_domain_model_echarts.alignment',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
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
        'tstamp' => [
            'exclude' => true,
            'label' => 'tstamp',
            'config' => [
                    'type' => 'input',
                    'size' => 20,
                    'eval' => 'datetime'
            ],
        ],
    ],
];
