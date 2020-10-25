<?php
namespace Jykj\Activity\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
/***
 *
 * This file is part of the "志愿者活动" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 yangshichang <yangshichang@ngoos.org>, 极益科技
 *
 ***/
/**
 * The repository for Volunteers
 */
class VolunteerRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
	/**
     * 
     * @param string keyword
     * 
     */
    public function findAll($keyword = '',$province=''){
        $query = $this->createQuery();
		$con = array();
		if($keyword != ''){
			$con[]=$query->logicalOr(array(
				$query->like('name', '%' . $keyword . '%'),
			    $query->like('telephone', '%' . $keyword . '%'),
			    $query->like('email', '%' . $keyword . '%')
            ));
		}

		if ($province != '') {
            $con[] = $query->equals('province', $province);
        }
		
        if(!empty($con)){
            $query->matching($query->logicalAnd($con));
        }
        
        $query->setOrderings(array(
            'crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
        ));
        
        $result = $query->execute();
        return $result;
    }

    /**
     * 检测数据是否重复
     * @param number $uid
     * @param string $telephone
     * @param string $email
     * @return int >0表示存在；=0表示不存在
     */
    public function checkData($uid=0,$telephone,$email){
        $query = $this->createQuery();
        $con = array();
        
        if ($telephone != '') {
            $con[] = $query->equals('telephone', $telephone);
        }
        
        if ($email != '') {
            $con[] = $query->equals('email', $email);
        }
        
        if($uid>0){
            $con[]=$query->logicalNot($query->equals('uid', $uid));
        }
        
        if(!empty($con)){
            $query->matching($query->logicalAnd($con));
        }
        $result = $query->execute()->count();
        return $result;
    }
    
    /**
     * 多选删除
     *
     * @param unknown $uids
     */
    public function deleteByUidstring($uids)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_activity_domain_model_volunteer');
        $affectedRows = $queryBuilder
        ->delete('tx_activity_domain_model_volunteer')
        ->where(
            $queryBuilder->expr()->in('uid',explode(",",$uids))
            )->execute();
            return $affectedRows;
    }
    
}
