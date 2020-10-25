<?php
namespace MiniFranske\FsMediaGallery\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Frans Saris <franssaris@gmail.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use MiniFranske\FsMediaGallery\Domain\Model\MediaAlbum;

/**
 * MediaAlbumRepository
 */
class MediaAlbumRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * @var array default ordering
     */
    protected $defaultOrderings = [
        'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
        'crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
    ];

    /**
     * @var array
     */
    protected $allowedAssetMimeTypes = [];

    /**
     * @var array
     */
    protected $albumUids = [];

    /**
     * @var bool
     */
    protected $useAlbumUidsAsExclude = false;

    /**
     * @var string
     */
    protected $assetsOrderBy = '';

    /**
     * @var string
     */
    protected $assetsOrderDirection = 'asc';

    /**
     * Set allowedAssetMimeTypes
     *
     * @param array $allowedAssetMimeTypes
     */
    public function setAllowedAssetMimeTypes($allowedAssetMimeTypes)
    {
        $this->allowedAssetMimeTypes = $allowedAssetMimeTypes;
    }

    /**
     * Get allowedAssetMimeTypes
     *
     * @return array $allowedAssetMimeTypes
     */
    public function getAllowedAssetMimeTypes()
    {
        return $this->allowedAssetMimeTypes;
    }

    /**
     * Get allowedAlbumUids
     *
     * @return array
     */
    public function getAlbumUids()
    {
        return $this->albumUids;
    }

    /**
     * Set allowedAlbumUids
     *
     * @param array $albumUids
     */
    public function setAlbumUids($albumUids)
    {
        $this->albumUids = $albumUids;
    }

    /**
     * Get useAlbumUidsAsExclude
     *
     * @return boolean
     */
    public function getUseAlbumUidsAsExclude()
    {
        return $this->useAlbumUidsAsExclude;
    }

    /**
     * Set useAlbumUidsAsExclude
     *
     * @param boolean $useAlbumUidsAsExclude
     */
    public function setUseAlbumUidsAsExclude($useAlbumUidsAsExclude)
    {
        $this->useAlbumUidsAsExclude = $useAlbumUidsAsExclude;
    }

    /**
     * Get assetsOrderBy
     *
     * @return string
     */
    public function getAssetsOrderBy()
    {
        return $this->assetsOrderBy;
    }

    /**
     * Set assetsOrderBy
     *
     * @param string $assetsOrderBy
     */
    public function setAssetsOrderBy($assetsOrderBy)
    {
        $this->assetsOrderBy = $assetsOrderBy;
    }

    /**
     * Get assetsOrderDirection
     *
     * @return string
     */
    public function getAssetsOrderDirection()
    {
        return $this->assetsOrderDirection;
    }

    /**
     * Set assetsOrderDirection
     *
     * @param string $assetsOrderDirection
     */
    public function setAssetsOrderDirection($assetsOrderDirection)
    {
        $this->assetsOrderDirection = $assetsOrderDirection;
    }

    /**
     * Get random sub album
     *
     * @param MediaAlbum|bool $parent parent MediaAlbum, FALSE for parent = 0 or NULL for no restriction by parent
     * @return \MiniFranske\FsMediaGallery\Domain\Model\MediaAlbum|NULL
     */
    public function findRandom($parent = null)
    {

        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Query $query */
        $query = $this->createQuery();
        $where = [];

        // restrict by parent album
        if ($parent !== null) {
            $where[] = 'parentalbum = ' . ($parent ? $parent->getUid() : 0);
        }

        // restrict by given uids
        if ($this->albumUids !== []) {
            $uids = [];
            foreach ($this->albumUids as $uid) {
                $uids[] = (int)$uid;
            }
            $where[] = 'uid ' . ($this->useAlbumUidsAsExclude ? 'NOT ' : '') . 'IN (' . implode(',', $uids) . ')';
        }

        $statement = 'SELECT * FROM sys_file_collection WHERE ' .
            (count($where) ? implode(' AND ', $where) : '1=1') .
            $this->getWhereClauseForEnabledFields() .
            ' ORDER BY RAND(NOW()) LIMIT 1';

        $query->statement($statement);
        $result = $query->execute();

        // todo: getFirst() might return an empty album
        /** @var \MiniFranske\FsMediaGallery\Domain\Model\MediaAlbum $mediaAlbum */
        $mediaAlbum = $result->getFirst();

        if ($mediaAlbum) {
            // set allowed asset mime types
            $mediaAlbum->setAllowedMimeTypes($this->allowedAssetMimeTypes);
            // set assets order
            $mediaAlbum->setAssetsOrderBy($this->assetsOrderBy);
            $mediaAlbum->setAssetsOrderDirection($this->assetsOrderDirection);
        }

        return $mediaAlbum;
    }

    /**
     * Find albums by parent album
     *
     * @param MediaAlbum $parentAlbum
     * @param boolean $excludeEmptyAlbums
     * @param string $orderBy Sort albums by: datetime|crdate|sorting
     * @param string $orderDirection Sort order: asc|desc
     * @return MediaAlbum[]
     */
    public function findByParentAlbum(
        MediaAlbum $parentAlbum = null,
        $excludeEmptyAlbums = true,
        $orderBy = 'sorting',
        $orderDirection = 'desc'
    ) {
        $excludeEmptyAlbums = filter_var($excludeEmptyAlbums, FILTER_VALIDATE_BOOLEAN);
        $query = $this->createQuery();
        $constraints = [];
        $constraints[] = $query->equals('parentalbum', $parentAlbum ?: 0);

        if ($this->albumUids !== []) {
            if ($this->useAlbumUidsAsExclude) {
                $constraints[] = $query->logicalNot($query->in('uid', $this->albumUids));
            } else {
                $constraints[] = $query->in('uid', $this->albumUids);
            }
        }

        $query->matching($query->logicalAnd($constraints));
        $query->setOrderings($this->getOrderingsSettings($orderBy, $orderDirection));
        $mediaAlbums = $query->execute()->toArray();

        foreach ($mediaAlbums as $key => $mediaAlbum) {
            /** @var $mediaAlbum \MiniFranske\FsMediaGallery\Domain\Model\MediaAlbum */
            // set allowed asset mime types
            $mediaAlbum->setAllowedMimeTypes($this->allowedAssetMimeTypes);
            // set assets order
            $mediaAlbum->setAssetsOrderBy($this->assetsOrderBy);
            $mediaAlbum->setAssetsOrderDirection($this->assetsOrderDirection);
            $mediaAlbum->setExcludeEmptyAlbums($excludeEmptyAlbums);

            // exclude if album is empty
            if (
                $excludeEmptyAlbums
                &&
                $mediaAlbum->getAssetsCount() === 0
                &&
                count($mediaAlbum->getAlbums()) === 0
            ) {
                unset($mediaAlbums[$key]);
            }
        }

        // Reset array keys and return albums
        return array_values($mediaAlbums);
    }

    /**
     * Find album by Uid
     *
     * @param integer $uid The identifier of the MediaAlbum to find
     * @param bool $respectStorage possibility to disable storage restriction
     * @return MediaAlbum|NULL The matching media album if found, otherwise NULL
     */
    public function findByUid($uid, $respectStorage = true)
    {

        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectSysLanguage(false);

        if (!$respectStorage) {
            $query->getQuerySettings()->setRespectStoragePage(false);
        }

        $constraints = [$query->equals('uid', (int)$uid)];

        if ($this->albumUids !== []) {
            if ($this->useAlbumUidsAsExclude) {
                $constraints[] = $query->logicalNot($query->in('uid', $this->albumUids));
            } else {
                $constraints[] = $query->in('uid', $this->albumUids);
            }
        }

        /** @var $mediaAlbum \MiniFranske\FsMediaGallery\Domain\Model\MediaAlbum */
        $mediaAlbum = $query->matching($query->logicalAnd($constraints))->execute()->getFirst();

        if ($mediaAlbum) {
            // set allowed asset mime types
            $mediaAlbum->setAllowedMimeTypes($this->allowedAssetMimeTypes);
            // set assets order
            $mediaAlbum->setAssetsOrderBy($this->assetsOrderBy);
            $mediaAlbum->setAssetsOrderDirection($this->assetsOrderDirection);
        }

        return $mediaAlbum;
    }

    /**
     * Find all albums
     *
     * @param boolean $excludeEmptyAlbums
     * @param string $orderBy Sort albums by: datetime|crdate|sorting
     * @param string $orderDirection Sort order: asc|desc
     * @return MediaAlbum[]
     */
    public function findAll($excludeEmptyAlbums = true, $orderBy = 'datetime', $orderDirection = 'desc')
    {
        $excludeEmptyAlbums = filter_var($excludeEmptyAlbums, FILTER_VALIDATE_BOOLEAN);
        $query = $this->createQuery();
        $query->setOrderings($this->getOrderingsSettings($orderBy, $orderDirection));

        if ($this->albumUids !== []) {
            if ($this->useAlbumUidsAsExclude) {
                $query->matching($query->logicalNot($query->in('uid', $this->albumUids)));
            } else {
                $query->matching($query->in('uid', $this->albumUids));
            }
        }

        $mediaAlbums = $query->execute()->toArray();

        foreach ($mediaAlbums as $key => $mediaAlbum) {
            /** @var $mediaAlbum \MiniFranske\FsMediaGallery\Domain\Model\MediaAlbum */
            // set allowed asset mime types
            $mediaAlbum->setAllowedMimeTypes($this->allowedAssetMimeTypes);
            // set assets order
            $mediaAlbum->setAssetsOrderBy($this->assetsOrderBy);
            $mediaAlbum->setAssetsOrderDirection($this->assetsOrderDirection);
            $mediaAlbum->setExcludeEmptyAlbums($excludeEmptyAlbums);

            // exclude if album is empty
            if (
                $excludeEmptyAlbums
                &&
                $mediaAlbum->getAssetsCount() === 0
                &&
                count($mediaAlbum->getAlbums()) === 0
            ) {
                unset($mediaAlbums[$key]);
            }
        }

        // Reset array keys and return albums
        return array_values($mediaAlbums);
    }

    /**
     * get the WHERE clause for the enabled fields of this TCA table
     * depending on the context
     *
     * @return string the additional where clause, something like " AND deleted=0 AND hidden=0"
     */
    protected function getWhereClauseForEnabledFields()
    {
        if (TYPO3_MODE === 'FE' && $GLOBALS['TSFE']->sys_page) {
            // frontend context
            $whereClause = $GLOBALS['TSFE']->sys_page->enableFields('sys_file_collection');
            $whereClause .= $GLOBALS['TSFE']->sys_page->deleteClause('sys_file_collection');
        } else {
            // backend context
            $whereClause = \TYPO3\CMS\Backend\Utility\BackendUtility::BEenableFields('sys_file_collection');
            $whereClause .= \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause('sys_file_collection');
        }
        return $whereClause;
    }

    /**
     * Get orderings settings. Returns an array like:
     * array(
     *  'foo' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
     *  'bar' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
     * )
     *
     * @param string $orderBy Sort albums by: datetime|crdate|sorting
     * @param string $orderDirection Sort order: asc|desc
     * @return array Orderings settings used by \TYPO3\CMS\Extbase\Persistence\QueryInterface->setOrderings()
     */
    protected function getOrderingsSettings($orderBy = 'sorting', $orderDirection = 'asc')
    {

        // check orderDirection
        if ($orderDirection === 'asc') {
            $orderDirection = \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING;
        } else {
            $orderDirection = \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING;
        }

        // set $orderingsSettings by orderBy and orderDirection
        switch ($orderBy) {
            case 'datetime':
                $orderingsSettings = [
                    'datetime' => $orderDirection,
                    'crdate' => $orderDirection
                ];
                break;
            case 'crdate':
                $orderingsSettings = ['crdate' => $orderDirection];
                break;
            default:
                // sorting
                $orderingsSettings = [
                    'sorting' => $orderDirection,
                    'crdate' => $orderDirection
                ];
        }

        return $orderingsSettings;
    }

}
