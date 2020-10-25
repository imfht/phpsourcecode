<?php
namespace Jykj\Teamlist\Domain\Repository;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 Yong Hui <huiyong@ngoos.org>, Jykj
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
/***
 *
 * This file is part of the "团队列表" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Yong Hui <huiyong@ngoos.org>, Jykj
 *
 ***/
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * The repository for Teams
 */
class TeamRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * 查询
     * 
     * @param unknown $keyword
     */
    public function findItemsList($keyword)
    {
        $query = $this->createQuery();
        $arr = [];
        if ($keyword != '') {
            $arr[] = $query->logicalOr(
            	[
				    $query->like('name', '%' . $keyword . '%'),
					$query->like('intro', '%' . $keyword . '%'),
					$query->like('detail', '%' . $keyword . '%')
				]
            );
        }
        if (!empty($arr)) {
            $query->matching($query->logicalAnd($arr));
        }
        $query->setOrderings(
	        [
			    'orders' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
			]
        );
        return $query->execute();
    }

    /**
     * 多选删除
     * 
     * @param unknown $uids
     */
    public function deleteByUidstring($uids)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_teamlist_domain_model_team');
        $affectedRows = $queryBuilder
        ->delete('tx_teamlist_domain_model_team')
        ->where(
            $queryBuilder->expr()->in('uid',explode(",",$uids))
        )->execute();
        return $affectedRows;
    }
}
