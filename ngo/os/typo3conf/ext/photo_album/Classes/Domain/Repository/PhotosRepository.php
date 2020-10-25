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
 * The repository for Photos
 */
class PhotosRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * 列表查询
     * @param string $folder
     * @param string $keyword
     */
    public function findAlls($folder,$keyword){
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file');
        $adnWhere="";
        if($keyword!=''){
            $adnWhere=$queryBuilder->expr()->like('tab2.title', $queryBuilder->createNamedParameter('%' . $keyword . '%'));
        }
        $photos = $queryBuilder
        ->select('tab1.uid as fuid', 'tab1.identifier','tab2.uid as muid','tab2.title','tab2.description','tab2.crdate')
        ->from('sys_file','tab1')
        ->join(
            'tab1',
            'sys_file_metadata',
            'tab2',
            $queryBuilder->expr()->eq('tab2.file', $queryBuilder->quoteIdentifier('tab1.uid'))
        )
        ->where(
            $queryBuilder->expr()->like('tab1.identifier', $queryBuilder->createNamedParameter('%' . $folder . '%'))
        )
        ->andWhere(
            $adnWhere
        )
        ->execute()
        ->fetchAll();
        return $photos;
    }
 
    /**
     * 通过uid查询表中一条记录
     * @param unknown $table
     * @param unknown $uid
     */
    public function querySingleRow($table,$uid){
        $row = GeneralUtility::makeInstance(ConnectionPool::class)
        ->getConnectionForTable($table)
        ->select(
            ['*'], // fields to select
            $table, // from
            ['uid' => $uid ] // where
        )
        ->fetch();
        return $row;
    }
    
    /**
     * 查询一条记录列表
     * @param int $fuid
     */
    public function findInfoByuid($fuid){
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file');
        $photo = $queryBuilder
        ->select('tab1.uid as fuid', 'tab1.identifier','tab2.uid as muid','tab2.title','tab2.description','tab2.crdate')
        ->from('sys_file','tab1')
        ->join(
            'tab1',
            'sys_file_metadata',
            'tab2',
            $queryBuilder->expr()->eq('tab2.file', $queryBuilder->quoteIdentifier('tab1.uid'))
        )
        ->where(
            $queryBuilder->expr()->eq('tab1.uid', $queryBuilder->createNamedParameter($fuid))
        )
        ->execute()
        ->fetch();
        return $photo;
    }
    
    /**
     * 删除指定表的记录
     * @param string $table
     * @param string $uid
     * @return unknown
     */
    public function deleteByUid($table,$uid)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $affectedRows = $queryBuilder
        ->delete($table)
        ->where(
            $queryBuilder->expr()->eq('uid',$queryBuilder->createNamedParameter($uid))
        )
        ->execute();
        return $affectedRows;
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
    
    /**
     * 插入数据
     * @param string $table
     * @param array $arrInsert
     * @return int  插入的最大id
     */
    public function insertRow($table,$arrInsert){
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $databaseConnectionForPages = $connectionPool->getConnectionForTable($table);
        $databaseConnectionForPages->insert(
            $table,$arrInsert
        );
        $maxUid = (int)$databaseConnectionForPages->lastInsertId($table);
        return $maxUid;
    }
}
