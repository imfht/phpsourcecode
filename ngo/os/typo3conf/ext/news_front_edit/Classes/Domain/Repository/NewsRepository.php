<?php
namespace TaoJiang\NewsFrontEdit\Domain\Repository;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Georg Ringer <typo3@ringerge.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * News repository with all the callable functionality
 *
 * @package TYPO3
 * @subpackage tx_news
 */
//class NewsRepository extends \Tx_News_Domain_Repository_NewsRepository {
class NewsRepository extends \GeorgRinger\News\Domain\Repository\NewsRepository{
    
    
    /**
     * news 列表
     * @param string keyword
     * 
     */
    public function findAllOrdering($categories,$keyword = ''){
        
        $query = $this->createQuery();
		
		$con = array();
		
		if($keyword != ''){
			$con[] = $query->like('title','%'.$keyword.'%');
		}
		
		if(!empty($categories)){
			$categories = explode(',',$categories);
			$constraints = array();
			foreach($categories as $category){
				$constraints[] = $query->contains('categories',$category);
			}
		}
		
		if(!empty($constraints)){
			$con[] = $query->logicalOr($constraints);
		}
		
		
        if(!empty($con)){
            $query->matching($query->logicalAnd($con));
        }
        
        $query->setOrderings(array(
            'istopnews' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
            'datetime' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
        ));
        
		//$GLOBALS['TYPO3_DB']->debugOutput = 2;
		
        $result = $query->execute();
        return $result;
    }
	
	
	/**
	 * 删除一组数据
	 * @param string $uids
	 * return void
	 */
	public function deleteByUidstring($uids){
	    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_news_domain_model_news');
	    $affectedRows = $queryBuilder
	    ->delete('tx_news_domain_model_news')
	    ->where(
            $queryBuilder->expr()->in('uid',explode(",",$uids))
        )->execute();
        return $affectedRows;
	}
}
