<?php
namespace Jykj\PhotoAlbum\Domain\Repository;


/***
 *
 * This file is part of the "相册管理" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 yangshichang <yangshichang@ngoos.org>, 极益科技
 *
 ***/
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
/**
 * The repository for Albums
 */
class AlbumRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * 查询列表
     * @param string $keyword
     * @return unknown
     */
    public function findAlls($keyword){
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file_collection');
        $rows = $queryBuilder
        ->select('*')
        ->from('sys_file_collection')
        ->where(
            $queryBuilder->expr()->like('title', $queryBuilder->createNamedParameter('%' . $keyword . '%'))
        )
        ->execute()
        ->fetchAll();
        return $rows;
    }
    
    /**
     * 根据uid，查询单条记录
     * @param string $table
     * @param int $uid
     * @return unknown
     */
    public function getSingleRow($table,$uid){
        $row = GeneralUtility::makeInstance(ConnectionPool::class)
        ->getConnectionForTable($table)
        ->select(
            ['*'], // fields to select
            'sys_file_collection', // from
            [ 'uid' => $uid ] // where
        )
        ->fetch();
        return $row;
    }
    
    /**
     * 修改数据
     * @param string $table
     * @param array $arrSet
     * @param array $arrWhere
     */
    public function updateRows($table,$arrSet,$arrWhere){
        GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table)
        ->update(
            $table,
            $arrSet, // set
            $arrWhere // where
        );
        return 1;
    }
    
}
