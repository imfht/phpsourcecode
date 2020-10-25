<?php
defined('TYPO3_MODE') || die();

if (isset($GLOBALS['TCA']['tx_news_domain_model_news'])) {
    $additionalColumns = [
        'related_fsmediaalbums' => [
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:fs_media_gallery/Resources/Private/Language/locallang_db.xlf:tx_news_domain_model_news.related_fsmediaalbums',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'sys_file_collection',
                'foreign_table' => 'sys_file_collection',
                'foreign_sortby' => 'sorting',
                'foreign_table_where' => ' AND (sys_file_collection.sys_language_uid = 0 OR sys_file_collection.l10n_parent = 0) AND sys_file_collection.pid = ###CURRENT_PID### AND sys_file_collection.uid != ###THIS_UID### ORDER BY sys_file_collection.sorting ASC, sys_file_collection.crdate DESC',
                'MM' => 'tx_news_domain_model_news_fsmediaalbums_mm',
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 5,
                'wizards' => [
                    'suggest' => [
                        'type' => 'suggest',
                    ],
                ],
            ],
        ],
    ];

    foreach ($GLOBALS['TCA']['tx_news_domain_model_news']['types'] as $type => $tmp) {
        $GLOBALS['TCA']['tx_news_domain_model_news']['types'][$type]['showitem'] .= ',--div--;LLL:EXT:fs_media_gallery/Resources/Private/Language/locallang_db.xlf:tx_fsmediagallery_domain_model_mediaalbum';
        // try to add field related_from after related_from
        if ($replacedTca = preg_replace('/(\s*)related_from(\s*)(;|,)/', 'related_from,related_fsmediaalbums$3',
            $GLOBALS['TCA']['tx_news_domain_model_news']['types'][$type]['showitem']
        )) {
            $GLOBALS['TCA']['tx_news_domain_model_news']['types'][$type]['showitem'] = $replacedTca;
        } else {
            $GLOBALS['TCA']['tx_news_domain_model_news']['types'][$type]['showitem'] .= ',related_fsmediaalbums';
        }
    }
    \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
        $GLOBALS['TCA']['tx_news_domain_model_news']['columns'],
        $additionalColumns
    );
}