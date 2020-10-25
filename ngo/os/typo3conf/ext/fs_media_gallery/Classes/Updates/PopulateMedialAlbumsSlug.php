<?php
declare(strict_types=1);

namespace MiniFranske\FsMediaGallery\Updates;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use MiniFranske\FsMediaGallery\Service\SlugService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\AbstractUpdate;

/**
 * Migrate EXT:realurl unique alias into empty news slugs
 *
 * If a lot of similar titles are used it might be a good a idea
 * to migrate the unique aliases from realurl to be sure that the same alias is used
 *
 * Requires existence of DB table tx_realurl_uniqalias, but EXT:realurl requires not to be installed
 * Will only appear if missing slugs found between realurl and news, respecting language and expire date from realurl
 * Copies values from 'tx_realurl_uniqalias.value_alias' to 'tx_news_domain_model_news.path_segment'
 */
class PopulateMedialAlbumsSlug extends AbstractUpdate
{

    /** @var SlugService */
    protected $slugService;

    public function __construct()
    {
        $this->slugService = GeneralUtility::makeInstance(SlugService::class);
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'Fill empty field "slug" of EXT:fs_media_gallery records';
    }

    /**
     * Get description
     *
     * @return string Longer description of this updater
     */
    public function getDescription(): string
    {
        return 'Fill empty field "slug" of EXT:fs_media_gallery records';
    }

    /**
     * @return string Unique identifier of this updater
     */
    public function getIdentifier(): string
    {
        return 'populateMedialAlbumsSlug';
    }

    /**
     * Checks if an update is needed
     *
     * @param string &$description The description for the update
     * @return bool Whether an update is needed (TRUE) or not (FALSE)
     */
    public function checkForUpdate(&$description): bool
    {
        if ($this->isWizardDone()) {
            return false;
        }
        $elementCount = $this->slugService->countOfSlugUpdates();
        if ($elementCount) {
            $description = sprintf('%s albums records possible to be updated', $elementCount);
        }
        return (bool)$elementCount;
    }

    /**
     * Performs the database update
     *
     * @param array &$databaseQueries Queries done in this update
     * @param string &$customMessage Custom message
     * @return bool
     */
    public function performUpdate(array &$databaseQueries, &$customMessage)
    {
        $queries = $this->slugService->performUpdateSlugs();
        if (!empty($queries)) {
            foreach ($queries as $query) {
                $databaseQueries[] = $query;
            }
            $this->markWizardAsDone();
            return true;
        }
        return false;
    }
}
