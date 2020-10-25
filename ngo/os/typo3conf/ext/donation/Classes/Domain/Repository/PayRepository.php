<?php
namespace Jykj\Donation\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
/***
 *
 * This file is part of the "Payment Module" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 王宏彬 <wanghongbin@ngoos.org>, 宁夏极益科技邮箱公司
 * 
 ***/
/**
 * The repository for Pays
 */
class PayRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    
    /**
     * @var array
     */
    protected $defaultOrderings = [
        'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
    ];

    /**
     * @param $keyword
     * @param $arguments
     */
    public function findAll($keyword = '', $arguments = [])
    {
        $query = $this->createQuery();
        $condition = [];
        if ($keyword != '') {
            $condition[] = $query->logicalOr(
            [
                $query->like('title', '%' . $keyword . '%'),
                $query->like('name', '%' . $keyword . '%'),
                $query->like('comment', '%' . $keyword . '%'),
                $query->like('email', '%' . $keyword . '%'),
                $query->like('telephone', '%' . $keyword . '%'),
                $query->like('module', '%' . $keyword . '%'),
                $query->like('payment', '%' . $keyword . '%'),
                $query->like('ordernumber', '%' . $keyword . '%')
            ]
            );
        }

        //search by fe
        if (!empty($arguments)) {
            foreach ($arguments as $key => $val) {
                if ($val) {
                    preg_match("/^pay\\-(.*)\$/is", $key, $matches);
                    if (isset($matches[1])) {
                        $key = $matches[1];
                        switch ($key) {
                            case 'money':
                                $condition[] = $query->greaterThanOrEqual($key, $val);
                                break;
                            case 'time-start':
                                $condition[] = $query->greaterThanOrEqual('crdate', strtotime($val));
                                break;
                            case 'time-end':
                                $condition[] = $query->lessThanOrEqual('crdate', strtotime($val) + 86400);
                                break;
                            case 'name':
                            case 'payment':
                                $condition[] = $query->like($key, $val . '%');
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
        }
        if (!empty($condition)) {
            $query->matching($query->logicalAnd($condition));
        }

        /*$query->setOrderings(array(
              'crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
          ));*/
        $result = $query->execute();
        return $result;
    }

    /**
	 * 删除一组数据
	 * @param string $uids
	 * return void
	 */
	public function deleteByUidstring($uids){
	    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_donation_domain_model_pay');
	    $affectedRows = $queryBuilder
	    ->delete('tx_donation_domain_model_pay')
	    ->where(
            $queryBuilder->expr()->in('uid',explode(",",$uids))
        )->execute();
        return $affectedRows;
	}
}
