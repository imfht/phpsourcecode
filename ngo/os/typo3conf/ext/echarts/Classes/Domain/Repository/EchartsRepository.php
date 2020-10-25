<?php
namespace Jykj\Echarts\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/***
 *
 * This file is part of the "统计数据图表" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 王宏彬 <wanghongbin816@gmail.com>, 宁夏极益科技邮箱公司
 *
 ***/
/**
 * The repository for Chartdatas
 */
class EchartsRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * 
     * @param string keyword
     * 
     */
    public function findAlls($echart = '',$keyword=''){
        $query = $this->createQuery();
		$con = array();

		if ($echart != '') {
            $con[] = $query->equals('echart', $echart);
        }

		if($keyword != ''){
			$con[]=$query->logicalOr(array(
				$query->like('title', '%' . $keyword . '%'),
			    $query->like('subtitle', '%' . $keyword . '%')
            ));
		}
		
        if(!empty($con)){
            $query->matching($query->logicalAnd($con));
        }
        
        $query->setOrderings(array(
            'tstamp' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
        ));
        
        $result = $query->execute();
        return $result;
    }

    /**
     * 多选删除
     *
     * @param unknown $uids
     */
    public function deleteByUidstring($uids)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_echarts_domain_model_echarts');
        $affectedRows = $queryBuilder
        ->delete('tx_echarts_domain_model_echarts')
        ->where($queryBuilder->expr()->in('uid',explode(",",$uids)))
        ->execute();
        return $affectedRows;
    }
}
