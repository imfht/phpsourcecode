<?php
namespace Jykj\Timeline\Domain\Repository;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 WHB <wanghonbin@ngoos.org>, 极益科技
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * The repository for Timelines
 */
class TimelineRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

	/**
     * @var array
     */
    protected $defaultOrderings = array(
        'eventdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
    );

    /**
     * 查询所有
     * @param  string $keyword [description]
     * @return [type]          [description]
     */
    public function findAlls($keyword='')
    {
    	$query = $this->createQuery();
        $condition = array();
        if ($keyword != '') {
            $condition[] = $query->logicalOr(array(
                $query->like('title', '%' . $keyword . '%')
            ));
        }

        if (!empty($condition)) {
            $query->matching($query->logicalAnd($condition));
        }

        $result = $query->execute();
        return $result;
    }
    
    /**
	 * 删除一组数据
	 * @param string $uids
	 * return void
	 */
	public function deleteByUidstring($uids){
	    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_timeline_domain_model_timeline');
	    $affectedRows = $queryBuilder
	    ->delete('tx_timeline_domain_model_timeline')
	    ->where(
	        $queryBuilder->expr()->in('uid',explode(",",$uids))
	    )->execute();
	    return $affectedRows;
	}
}