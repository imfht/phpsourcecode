<?php
namespace Jykj\Activity\Domain\Repository;


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
 * The repository for Activities
 */
class ActivityRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * 查询活动列表信息
     * 
     * @param string $keyword
     */
    public function findAlls($keyword)
    {
        $query = $this->createQuery();
        $arr = [];
        $arr[] = $query->equals('deltag', '0');
        if ($keyword != '') {
            $arr[] = $query->like('name', '%' . $keyword . '%');
        }
        if (!empty($arr)) {
            $query->matching($query->logicalAnd($arr));
        }
        $query->setOrderings(
            [
                'sendstat' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
            ]
        );
        return $query->execute();
    }

    /**
     * 查询当前在线的活动
     *
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    public function findOnlineActivity()
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(FALSE);
        $arr = [];
        //未删除的
        $arr[] = $query->equals('deltag', '0');
        //当前时间之前的
        $arr[] = $query->lessThan('overtime', time());
        //还未下线的
        $arr[] = $query->equals('sendstat', '1');
        
        if (!empty($arr)) {
            $query->matching($query->logicalAnd($arr));
        }
        $query->setOrderings(
            [
                'crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
            ]
        );
        $res = $query->execute();
        return $res;
    }


    /**
     * 小程序端查询正在进行的活动
     * 
     * @param int $pagesize
     * @param int $nowpage
     */
    public function findSignNowActivities($pagesize, $nowpage)
    {
        $query = $this->createQuery();
        $arr = [];
        $arr[] = $query->equals('sendstat', 1);

        //发布的活动
        $arr[] = $query->equals('deltag', '0');
        if ($nowpage > 0) {

            //$arr[]=$query->greaterThanOrEqual('overtime',time());//活动结束就不能签到
            //判断当前是周几
            $w = date("w");
            if ($w == 0) {
                $w = 7;
            }

            //普通活动 判断结束时间；常态化活动判断当前是周几
            $arr[] = $query->logicalOr(
                [
                    $query->logicalAnd([$query->greaterThanOrEqual('overtime', time()), $query->equals('way', 0)]),
                    $query->logicalAnd([$query->equals('way', 1), $query->equals('week', $w)])
                ]
            );
        }
        if (!empty($arr)) {
            $query->matching($query->logicalAnd($arr));
        }
        if ($nowpage > 0) {
            $query->setOrderings(
                [
                    'crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
                    'sttime' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
                ]
            );
            $query->setOffset(((int) $nowpage - 1) * (int) $pagesize);

            //从第几条记录开始
        } else {

            //首页显示倒序排列
            $query->setOrderings(
            [
                'crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
                'sttime' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
            ]
            );
        }

        //分页查询
        $query->setLimit((int) $pagesize);

        //每页记录数
        return $query->execute();
    }
    
    /**
     * 多选删除
     *
     * @param unknown $uids
     */
    public function deleteByUidstring($uids)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_activity_domain_model_activity');
        $affectedRows = $queryBuilder
        ->delete('tx_activity_domain_model_activity')
        ->where(
            $queryBuilder->expr()->in('uid',explode(",",$uids))
            )->execute();
            return $affectedRows;
    }
}
