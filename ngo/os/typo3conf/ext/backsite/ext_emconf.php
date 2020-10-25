<?php

/**
 * Extension Manager/Repository config file for ext "backsite".
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'backsite',
    'description' => '',
    'category' => 'templates',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
            'fluid_styled_content' => '9.5.0-9.5.99',
            'rte_ckeditor' => '9.5.0-9.5.99'
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Jiyikeji\\Backsite\\' => 'Classes'
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'yangshichang',
    'author_email' => 'yangshichang@ngoos.org',
    'author_company' => 'jiyikeji',
    'version' => '1.0.0',
];
