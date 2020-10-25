<?php
/*
 * This source file is proprietary of Beech Applications bv.
 * Created by: Ruud Silvrants
 * Date: 30/04/2019
 * All code (c) Beech Applications bv. all rights reserverd
 */

namespace MiniFranske\FsMediaGallery\Service;


use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SlugService
 * @package MiniFranske\FsMediaGallery\Service
 */
class SlugService
{
    protected $tableName = 'sys_file_collection';
    protected $slugFieldName = 'slug';

    /**
     * @return int
     */
    public function countOfSlugUpdates(): int
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->tableName);
        $queryBuilder->getRestrictions()->removeAll();
        $elementCount = $queryBuilder->count('uid')
            ->from($this->tableName)
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq(
                        $this->slugFieldName,
                        $queryBuilder->createNamedParameter('', \PDO::PARAM_STR)
                    ),
                    $queryBuilder->expr()->isNull($this->slugFieldName)
                )
            )
            ->execute()->fetchColumn(0);

        return $elementCount;
    }

    /**
     * @return array
     */
    public function performUpdateSlugs(): array
    {
        $databaseQueries = [];

        /** @var Connection $connection */
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($this->tableName);
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $connection->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeAll();
        $fieldConfig = $GLOBALS['TCA'][$this->tableName]['columns'][$this->slugFieldName]['config'];
        /** @var SlugHelper $slugHelper */
        $slugHelper = GeneralUtility::makeInstance(SlugHelper::class, $this->tableName, $this->slugFieldName,
            $fieldConfig);

        $statement = $queryBuilder->select('*')
            ->from($this->tableName)
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq(
                        $this->slugFieldName,
                        $queryBuilder->createNamedParameter('', \PDO::PARAM_STR)
                    ),
                    $queryBuilder->expr()->isNull($this->slugFieldName)
                )
            )
            ->execute();
        while ($record = $statement->fetch()) {
            //Use the core slughelper which is also used in the BE form
            $slug = $slugHelper->generate($record, $record['pid']);
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = $connection->createQueryBuilder();
            $queryBuilder->update($this->tableName)
                ->where(
                    $queryBuilder->expr()->eq(
                        'uid',
                        $queryBuilder->createNamedParameter($record['uid'], \PDO::PARAM_INT)
                    )
                )
                ->set($this->slugFieldName, $slug);
            $databaseQueries[] = $queryBuilder->getSQL();
            $queryBuilder->execute();
        }

        return $databaseQueries;
    }

    /**
     * Count valid entries from EXT:realurl table tx_realurl_uniqalias which can be migrated
     * Checks also for existance of third party extension table 'tx_realurl_uniqalias'
     * EXT:realurl requires not to be installed
     *
     * @return int
     */
    public function countOfRealurlAliasMigrations(): int
    {
        $elementCount = 0;
        // Check if table 'tx_realurl_uniqalias' exists
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_realurl_uniqalias');
        $schemaManager = $queryBuilder->getConnection()->getSchemaManager();
        if ($schemaManager->tablesExist(['tx_realurl_uniqalias']) === true) {
            // Count valid aliases for news
            $queryBuilder->getRestrictions()->removeAll();
            $elementCount = $queryBuilder->selectLiteral('COUNT(DISTINCT ' . $this->tableName . '.uid)')
                ->from('tx_realurl_uniqalias')
                ->join(
                    'tx_realurl_uniqalias',
                    $this->tableName,
                    $this->tableName,
                    $queryBuilder->expr()->eq(
                        'tx_realurl_uniqalias.value_id',
                        $queryBuilder->quoteIdentifier($this->tableName . '.uid')
                    )
                )
                ->where(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->orX(
                            $queryBuilder->expr()->eq(
                                $this->tableName . '.' . $this->slugFieldName,
                                $queryBuilder->createNamedParameter('', \PDO::PARAM_STR)
                            ),
                            $queryBuilder->expr()->isNull($this->tableName . '.' . $this->slugFieldName)
                        ),
                        $queryBuilder->expr()->eq(
                            $this->tableName . '.sys_language_uid',
                            'tx_realurl_uniqalias.lang'
                        ),
                        $queryBuilder->expr()->eq(
                            'tx_realurl_uniqalias.tablename',
                            $queryBuilder->createNamedParameter($this->tableName, \PDO::PARAM_STR)
                        ),
                        $queryBuilder->expr()->orX(
                            $queryBuilder->expr()->eq(
                                'tx_realurl_uniqalias.expire',
                                $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                            ),
                            $queryBuilder->expr()->gte(
                                'tx_realurl_uniqalias.expire',
                                $queryBuilder->createNamedParameter($GLOBALS['ACCESS_TIME'], \PDO::PARAM_INT)
                            )
                        )
                    )
                )
                ->execute()->fetchColumn(0);
        }
        return $elementCount;
    }

    /**
     * Perform migration of EXT:realurl unique alias into empty news slugs
     *
     * @return array
     */
    public function performRealurlAliasMigration(): array
    {
        $databaseQueries = [];

        // Check if table 'tx_realurl_uniqalias' exists
        /** @var QueryBuilder $queryBuilderForRealurl */
        $queryBuilderForRealurl = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_realurl_uniqalias');
        $schemaManager = $queryBuilderForRealurl->getConnection()->getSchemaManager();
        if ($schemaManager->tablesExist(['tx_realurl_uniqalias']) === true) {
            /** @var Connection $connection */
            $connection = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable($this->tableName);
            $queryBuilder = $connection->createQueryBuilder();

            // Get entries to update
            $statement = $queryBuilder
                ->selectLiteral(
                    'DISTINCT ' . $this->tableName . '.uid, tx_realurl_uniqalias.value_alias, ' . $this->tableName . '.uid'
                )
                ->from($this->tableName)
                ->join(
                    $this->tableName,
                    'tx_realurl_uniqalias',
                    'tx_realurl_uniqalias',
                    $queryBuilder->expr()->eq(
                        $this->tableName . '.uid',
                        $queryBuilder->quoteIdentifier('tx_realurl_uniqalias.value_id')
                    )
                )
                ->where(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->orX(
                            $queryBuilder->expr()->eq(
                                $this->tableName . '.' . $this->slugFieldName,
                                $queryBuilder->createNamedParameter('', \PDO::PARAM_STR)
                            ),
                            $queryBuilder->expr()->isNull($this->tableName . '.' . $this->slugFieldName)
                        ),
                        $queryBuilder->expr()->eq(
                            $this->tableName . '.sys_language_uid',
                            'tx_realurl_uniqalias.lang'
                        ),
                        $queryBuilder->expr()->eq(
                            'tx_realurl_uniqalias.tablename',
                            $queryBuilder->createNamedParameter($this->tableName, \PDO::PARAM_STR)
                        ),
                        $queryBuilder->expr()->orX(
                            $queryBuilder->expr()->eq(
                                'tx_realurl_uniqalias.expire',
                                $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                            ),
                            $queryBuilder->expr()->gte(
                                'tx_realurl_uniqalias.expire',
                                $queryBuilder->createNamedParameter($GLOBALS['ACCESS_TIME'], \PDO::PARAM_INT)
                            )
                        )
                    )
                )
                ->execute();

            // Update entries
            while ($record = $statement->fetch()) {
                $slug = (string)$record['value_alias'];
                $queryBuilder = $connection->createQueryBuilder();
                $queryBuilder->update($this->tableName)
                    ->where(
                        $queryBuilder->expr()->eq(
                            'uid',
                            $queryBuilder->createNamedParameter($record['uid'], \PDO::PARAM_INT)
                        )
                    )
                    ->set($this->slugFieldName, $slug);
                $databaseQueries[] = $queryBuilder->getSQL();
                $queryBuilder->execute();
            }
        }

        return $databaseQueries;
    }

}