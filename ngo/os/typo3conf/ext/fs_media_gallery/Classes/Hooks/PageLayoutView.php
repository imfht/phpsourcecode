<?php
namespace MiniFranske\FsMediaGallery\Hooks;

/*
 * This source file is proprietary property of Beech Applications B.V.
 * Date: 28-09-2016
 * All code (c) Beech Applications B.V. all rights reserved
 */
use TYPO3\CMS\Backend\Template\DocumentTemplate;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\Connection;

/**
 * Class PageLayoutView
 */
class PageLayoutView
{

    /**
     * Path to the locallang file
     *
     * @var string
     */
    const LLPATH = 'LLL:EXT:fs_media_gallery/Resources/Private/Language/locallang_be.xlf:';

    /**
     * Table information
     *
     * @var array
     */
    public $tableData = [];

    /**
     * FlexForm information
     *
     * @var array
     */
    public $flexFormData = [];

    /**
     * Returns information about this extension's pi1 plugin
     *
     * @param array $params Parameters to the hook
     * @return string Information about pi1 plugin
     */
    public function getExtensionSummary(array $params)
    {
        /** @var \TYPO3\CMS\Backend\View\PageLayoutView $pageLayoutView */
        $pageLayoutView = $params['pObj'];
        $row = $params['row'];
        $this->flexFormData = GeneralUtility::xml2array($row['pi_flexform']);
        $action = $this->getAction();

        $result = '<strong>' . $pageLayoutView->linkEditContent($this->getLanguageService()->sL(self::LLPATH . 'mediagallery.title', true), $row) . '</strong><br>';
        $result .= $this->getDisplayMode($action);

        $result .= '<hr>';

        if (in_array($action, ['showAlbum', 'showAlbumByConfig'], true)) {
            $this->getAlbumSelection();
        } else {
            $this->getAlbumsSelection();
        }
        $this->getStartingPoint();

        $result .= $this->renderSettingsAsTable();

        return $result;
    }

    /**
     * Get Action
     *
     * @return string
     */
    protected function getAction()
    {
        // if flexForm data is found
        $actions = $this->getFieldFromFlexform('switchableControllerActions');
        $action = '';

        if (!empty($actions)) {
            $actionList = GeneralUtility::trimExplode(';', $actions);

            // translate the first action into its translation
            $action = str_replace('MediaAlbum->', '', $actionList[0]);
        }
        return $action;
    }

    /**
     * Get display mode
     *
     * @param string $action
     * @return string
     */
    protected function getDisplayMode($action)
    {
        switch ($action) {
            case 'showalbum';
                $actionTranslationKey = 'showAlbumByParam';
                break;
            default:
                $actionTranslationKey = $action;
        }
        return $this->getLanguageService()->sL(self::LLPATH . 'flexforms.mediagallery.switchableControllerActions.I.' . $actionTranslationKey);
    }

    /**
     * Get field value from flexform configuration,
     * including checks if flexform configuration is available
     *
     * @param string $key name of the key
     * @param string $sheet name of the sheet
     * @return string|NULL if nothing found, value if found
     */
    public function getFieldFromFlexform($key, $sheet = 'general')
    {
        $flexform = $this->flexFormData;
        if (isset($flexform['data'])) {
            $flexform = $flexform['data'];
            if (is_array($flexform) && is_array($flexform[$sheet]) && is_array($flexform[$sheet]['lDEF'])
                && is_array($flexform[$sheet]['lDEF'][$key]) && isset($flexform[$sheet]['lDEF'][$key]['vDEF'])
            ) {
                return $flexform[$sheet]['lDEF'][$key]['vDEF'];
            }
        }

        return null;
    }

    /**
     * Render selected albums
     *
     * @return void
     */
    public function getAlbumSelection()
    {
        $albumUid = (int)$this->getFieldFromFlexform('settings.mediaAlbum');
        if ((int)$albumUid > 0) {
            // Album record
            $rowSysFileCollectionRecords = $this->getDatabaseConnection()->select(['*'], 'sys_file_collection', [
                'uid' => (int)$albumUid,
                'deleted' => 0,
            ])->fetchAll();

            $albums = [];
            foreach ($rowSysFileCollectionRecords as $record) {
                $albums[] = htmlspecialchars(BackendUtilityCore::getRecordTitle('sys_file_collection', $record));
            }

            $this->tableData[] = [
                $this->getLanguageService()->sL(self::LLPATH . 'flexforms.mediagallery.mediaAlbum') .
                '<br />',
                implode(', ', $albums)
            ];
        }
    }

    /**
     * Render selected albums
     *
     * @param bool $showCategoryMode show the category conjunction
     * @return void
     */
    public function getAlbumsSelection()
    {
        $filterMode = '';
        $albums = [];

        $albumUids = GeneralUtility::intExplode(',', $this->getFieldFromFlexform('settings.mediaAlbumsUids'), true);
        if (count($albumUids) > 0) {

            // Filter mode
            $selectedFilerMode = $this->getFieldFromFlexform('settings.useAlbumFilterAsExclude');

            if ($selectedFilerMode !== '') {
                $filterMode = $this->getLanguageService()->sL(self::LLPATH . 'flexforms.general.I.inherit');
                $filterMode = '<span style="font-weight:normal;font-style:italic">(' . htmlspecialchars($filterMode) . ')</span>';
            }

            // Album records
            $q = $this->getDatabaseConnection()->createQueryBuilder();

            $q->getRestrictions()
                ->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

            $quotedIdentifiers = $q->createNamedParameter($albumUids, Connection::PARAM_INT_ARRAY);
            $q->select('*')
                ->from('sys_file_collection')
                ->where(
                    $q->expr()->in('uid', $quotedIdentifiers)
                );

            $rowSysFileCollectionRecords = $q->execute()->fetchAll();

            foreach ((array)$rowSysFileCollectionRecords as $record) {
                $albums[] = htmlspecialchars(BackendUtilityCore::getRecordTitle('sys_file_collection', $record));
            }

            $this->tableData[] = [
                $this->getLanguageService()->sL(self::LLPATH . 'flexforms.mediagallery.mediaAlbumsUids') .
                '<br />' . $filterMode,
                implode(', ', $albums)
            ];
        }
    }

    /**
     * Get the startingPoint
     *
     * @return void
     */
    public function getStartingPoint()
    {
        $value = $this->getFieldFromFlexform('settings.startingpoint');

        if (!empty($value)) {
            $pagesOut = [];

            $q = $this->getDatabaseConnection('pages')->createQueryBuilder();
            $quotedIdentifiers = $q->createNamedParameter(GeneralUtility::intExplode(',', $value, true), Connection::PARAM_INT_ARRAY);

            $q->select('*')
                ->from('pages')
                ->where(
                    $q->expr()->in('uid', $quotedIdentifiers)
                );

            $rawPagesRecords = $q->execute()->fetchAll();


            foreach ((array)$rawPagesRecords as $page) {
                $pagesOut[] = htmlspecialchars(BackendUtilityCore::getRecordTitle('pages',
                        $page)) . ' (' . $page['uid'] . ')';
            }

            $recursiveLevel = (int)$this->getFieldFromFlexform('settings.recursive');
            $recursiveLevelText = '';
            if ($recursiveLevel === 250) {
                $recursiveLevelText = $this->getLanguageService()->sL('LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:recursive.I.5');
            } elseif ($recursiveLevel > 0) {
                $recursiveLevelText = $this->getLanguageService()->sL('LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:recursive.I.' . $recursiveLevel);
            }

            if (!empty($recursiveLevelText)) {
                $recursiveLevelText = '<br />' .
                    $this->getLanguageService()->sL('LLL:EXT:lang/locallang_general.xlf:LGL.recursive', true) . ' ' .
                    $recursiveLevelText;
            }

            $this->tableData[] = [
                $this->getLanguageService()->sL('LLL:EXT:lang/locallang_general.php:LGL.startingpoint'),
                implode(', ', $pagesOut) . $recursiveLevelText
            ];
        }
    }

    /**
     * Render the settings as table for Web>Page module
     * System settings are displayed in mono font
     *
     * @return string
     */
    protected function renderSettingsAsTable()
    {
        if (count($this->tableData) == 0) {
            return '';
        }

        $content = '';
        foreach ($this->tableData as $line) {
            $content .= '<strong>' . $line[0] . '</strong>' . ' ' . $line[1] . '<br />';
        }

        return '<pre style="white-space:normal">' . $content . '</pre>';
    }

    /**
     * Return language service instance
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    public function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @param string $table
     * @return \TYPO3\CMS\Core\Database\Connection
     */
    public function getDatabaseConnection(string $table = 'sys_file_collection')
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table);
    }
}
