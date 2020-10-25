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
 * The repository for Signups
 */
class SignupRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * 查询签到的志愿者
     * 
     * @param $activity
     * @param $keyword
     * @param $tab
     */
    public function findAlls($activity, $tab, $keyword)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(FALSE);

        //忽略pid
        $arr = [];
        $arr[] = $query->equals('activityuid.uid', $activity);

        // // 报名
        // if ($tab=='signin') {
        //     $arr[] = $query->equals('status', 0);
        // }

        // // 签到
        // if ($tab=='checkin') {
        //     $arr[] = $query->greaterThanOrEqual('status', 1);
        // }

        //指定活动
        if ($keyword != '') {
            $arr[] = $query->logicalOr(
            [
                $query->like('volunteer.name', '%' . $keyword . '%'),
                $query->like('volunteer.telephone', '%' . $keyword . '%')
            ]
            );
        }
        if (!empty($arr)) {
            $query->matching($query->logicalAnd($arr));
        }
        $query->setOrderings(
            [
                'crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
            ]
        );
        return $query->execute();
    }

    /**
     * 业务后台查询每个志愿者签到的活动
     * 
     * @param int $volunteer
     * @param string $keyword
     */
    public function findMyActivity($volunteer, $keyword)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(FALSE);

        //忽略pid
        $arr = [];
        $arr[] = $query->equals('volunteer.uid', $volunteer);
        if ($keyword != '') {
            $arr[] = $query->logicalOr(
                [
                    $query->like('activityuid.name', '%' . $keyword . '%')
                ]
                );
        }
        if (!empty($arr)) {
            $query->matching($query->logicalAnd($arr));
        }
        $query->setOrderings(
            [
                'crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
            ]
        );
        return $query->execute();
    }

    /**
     * 查询每个志愿者的活动
     * 
     * @param int $volunteer
     * @param int $pagesize
     * @param int $nowpage
     * @param $stdate
     * @param $eddate
     */
    public function findMySignActivity($volunteer, $pagesize, $nowpage, $stdate, $eddate)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(FALSE);

        //忽略pid
        $arr = [];
        $arr[] = $query->equals('volunteer.uid', $volunteer);

        //指定活动
        if ($stdate != "") {

            //普通活动 判断结束时间；常态化活动判断当前是周几
            $arr[] = $query->logicalOr(
                [
                    $query->logicalAnd([$query->greaterThanOrEqual('activityuid.sttime', strtotime($stdate . " 00:00:00")), $query->equals('activityuid.way', 0)]),
                    $query->logicalAnd([$query->equals('activityuid.way', 1), $query->greaterThanOrEqual('activityuid.tstamp', strtotime($stdate . " 00:00:00"))])
                ]
            );
        }
        if ($eddate != "") {

            //$arr[]=$query->lessThanOrEqual('activityuid.sttime',strtotime($eddate." 23:59:59"));
            $arr[] = $query->logicalOr(
                [
                    $query->logicalAnd([$query->lessThanOrEqual('activityuid.sttime', strtotime($eddate . " 23:59:59")), $query->equals('activityuid.way', 0)]),
                    $query->logicalAnd([$query->equals('activityuid.way', 1), $query->lessThanOrEqual('activityuid.tstamp', strtotime("2050-12-31 00:00:00"))])
                ]
            );
        }
        if (!empty($arr)) {
            $query->matching($query->logicalAnd($arr));
        }
        $query->setOrderings(
            [
                'signtime' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
            ]
        );

        //分页查询
        $query->setOffset(((int) $nowpage - 1) * (int) $pagesize);

        //从第几条记录开始
        $query->setLimit((int) $pagesize);

        //每页记录数
        return $query->execute();
    }

    /**
     * 查询是否已经签到
     * 
     * @param int $telephone
     * @param int $activity
     */
    public function findIsSign($telephone, $activity,$flag)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(FALSE);

        //忽略pid
        $arr = [];
        $arr[] = $query->equals('volunteer.telephone', $telephone);

        //指定活动
        $arr[] = $query->equals('activityuid.uid', $activity);
        if ($flag==1) {
            //签到
            $arr[] = $query->greaterThanOrEqual('status', 1);
        }
        if (!empty($arr)) {
            $query->matching($query->logicalAnd($arr));
        }
        $res = $query->execute();
        return $res;
    }
    
    /**
     * 志愿者活动参与统计
     * @param string $sttime
     * @param string $overtime
     * @return unknown
     */
    public function zyzHdTj($sttime,$overtime){
        //志愿者活动参与统计
        $strWhere = " 1=1 and checktime>0 ";
        if($sttime!=""){
            $strWhere .=" and checktime>=".strtotime($sttime."-01 00:00:00");
        }
        if($overtime!=""){
            $strWhere .=" and checktime<=".strtotime("$overtime +1 month -1 day");
        }
//         $couSql="select uid,name,case when num is null then 0 else num end num
// 	    				from tx_dicts_domain_model_dictitem c left join
// 	    				(SELECT b.tag,count(1) num FROM tx_activity_domain_model_signup a,
// 	    				tx_activity_domain_model_activity b where a.activityuid=b.uid and a.deleted=0
// 	    				and a.hidden=0 and b.deleted=0 and b.hidden=0 ".$strWhere." group by b.tag) d
// 	    				on c.uid=d.tag where deleted=0 and dicttype=2 order by sort asc";
//         $tagList = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows("*", "(".$couSql.") tab", "","","");
        
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_activity_domain_model_signup');
        $records = $queryBuilder
        ->add('select','tab2.tag,count(distinct tab1.uid) as num')
        ->from('tx_activity_domain_model_signup','tab1')
        ->join(
            'tab1',
            'tx_activity_domain_model_activity',
            'tab2',
            $queryBuilder->expr()->eq('tab2.uid', $queryBuilder->quoteIdentifier('tab1.activityuid'))
        )
        ->where($strWhere)
        ->groupBy('tab2.tag')
        ->execute()
        ->fetchAll();
        return $records;
    }
    
    /**
     *志愿者参与类别及人数统计
     * @param string $sttime
     * @param string $overtime
     * @return unknown
     */
    public function zyzCylbAndRs($sttime,$overtime){
        $strWhere = " 1=1 and checktime>0";
        if($sttime!=""){
            $strWhere .=" and checktime>=".strtotime($sttime."-01 00:00:00");
        }
        if($overtime!=""){
            $strWhere .=" and checktime<=".strtotime("$overtime +1 month -1 day");
        }
        // $couSql="select uid,name,case when tnum is null then 0 else tnum end tnum,
        // 		case when pnum is null then 0 else pnum end pnum
        // 			from tx_dicts_domain_model_dictitem c left join
        // 			(SELECT b.tag,count(1) tnum,count(distinct useruid) pnum FROM tx_activity_domain_model_signup a,
        // 			tx_activity_domain_model_activity b where a.activityuid=b.uid and a.deleted=0
        // 			and a.hidden=0 and b.deleted=0 and b.hidden=0 ".$strWhere." group by b.tag ) d
        // 			on c.uid=d.tag where deleted=0 and dicttype=2 order by sort asc";
        // $list = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows("*", "(".$couSql.") tab", "","","");
        // //统计总数
        // $sumall="select sum(tnum) tallnum,sum(pnum) pallnum from
        // 		(SELECT b.tag,count(1) tnum,count(distinct useruid) pnum FROM
        // 		tx_activity_domain_model_signup a,tx_activity_domain_model_activity b
        // 		where a.activityuid=b.uid and a.deleted=0 and a.hidden=0 and
        // 		b.deleted=0 and b.hidden=0 ".$strWhere." group by b.tag ) tab";
        // $info = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow("*","(".$sumall.") t","");
        
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_activity_domain_model_signup');
        $records = $queryBuilder
        ->add('select','tab2.tag,count(distinct tab1.uid) as tnum,count(distinct tab1.volunteer) as pnum')
        ->from('tx_activity_domain_model_signup','tab1')
        ->join(
            'tab1',
            'tx_activity_domain_model_activity',
            'tab2',
            $queryBuilder->expr()->eq('tab2.uid', $queryBuilder->quoteIdentifier('tab1.activityuid'))
        )
        ->where($strWhere)
        ->groupBy('tab2.tag')
        ->execute()
        ->fetchAll();
        return $records;
    }
    
    /**
     *志愿者年龄分布统计
     * @param string $sttime
     * @param string $overtime
     * @return unknown
     */
    public function zyzNlTj($sttime,$overtime){
        $strWhere = "  and checktime>0";
        if($sttime!=""){
            $strWhere .=" and checktime>=".strtotime($sttime."-01 00:00:00");
        }
        if($overtime!=""){
            $strWhere .=" and checktime<=".strtotime("$overtime +1 month -1 day");
        }
        $couSql="select biryear status,sum(tnum) pid,sum(pnum) uid from
        		(select case when DATE_FORMAT(birthday, '%Y')>=2010 then 0
        		when  DATE_FORMAT(birthday, '%Y')>=1980 and DATE_FORMAT(birthday, '%Y')<=2009 then 1
        		when  DATE_FORMAT(birthday, '%Y')>=1960 and DATE_FORMAT(birthday, '%Y')<=1979 then 2
        		when  DATE_FORMAT(birthday, '%Y')>=1940 and DATE_FORMAT(birthday, '%Y')<=1959 then 3
        		when  DATE_FORMAT(birthday, '%Y')<=1939 then 4 end biryear ,count(1) tnum,count(distinct volunteer) pnum
        		FROM tx_activity_domain_model_signup a, tx_activity_domain_model_volunteer b where a.volunteer=b.uid and a.deleted=0
        		and a.hidden=0 and b.deleted=0 ".$strWhere." group by DATE_FORMAT(birthday, '%Y')) tt group by biryear";
        // $list = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows("*", "(".$couSql.") tab", "","","biryear asc");
        
        // //统计总数
        // $sumall="SELECT count(1) tallnum,count(distinct useruid) pallnum FROM
        // 		tx_activity_domain_model_signup a,fe_users b
        // 		where a.useruid=b.uid and a.deleted=0 and a.hidden=0 and b.deleted=0 ".$strWhere;
        // $info = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow("*","(".$sumall.") t","");
        
        $query = $this->createQuery();
        $query->statement($couSql,array());
        $result=$query->execute();
        return $result;
    }
    
    /**
     *按照类型统计
     * @param string $sttime
     * @param string $overtime
     * @return unknown
     */
    public function lxTj($sttime,$overtime){
        $strWhere = " 1=1 and checktime>0";
        if($sttime!=""){
            $strWhere .=" and checktime>=".strtotime($sttime."-01 00:00:00");
        }
        if($overtime!=""){
            $strWhere .=" and checktime<=".strtotime("$overtime +1 month -1 day");
        }
        
//         $couSql="select b.types,b.tag,count(1) tnum,count(distinct volunteer) pnum FROM tx_activity_domain_model_signup a,
// 		tx_activity_domain_model_activity b where a.activityuid=b.uid and a.deleted=0
// 		and a.hidden=0 and b.deleted=0 and b.hidden=0 ".$strWhere." group by b.types,b.tag ";
        
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_activity_domain_model_signup');
        $records = $queryBuilder
        ->add('select','tab2.types,tab2.tag,count(1) tnum,count(distinct volunteer) pnum')
        ->from('tx_activity_domain_model_signup','tab1')
        ->join(
            'tab1',
            'tx_activity_domain_model_activity',
            'tab2',
            $queryBuilder->expr()->eq('tab2.uid', $queryBuilder->quoteIdentifier('tab1.activityuid'))
        )
        ->where($strWhere)
        ->groupBy('tab2.types')
        ->addGroupBy('tab2.tag')
        ->execute()
        ->fetchAll();
        return $records;
    }
    
    /**
     *按照社区统计
     * @param string $sttime
     * @param string $overtime
     * @return unknown
     */
    public function sqTj($sttime,$overtime){
        $strWhere = " 1=1 and checktime>0";
        if($sttime!=""){
            $strWhere .=" and checktime>=".strtotime($sttime."-01 00:00:00");
        }
        if($overtime!=""){
            $strWhere .=" and checktime<=".strtotime("$overtime +1 month -1 day");
        }
        
        // $couSql="select uid,name,case when tnum is null then 0 else tnum end tnum
        // 			from tx_dicts_domain_model_dictitem c left join
        // 			(SELECT b.community,count(1) tnum FROM tx_activity_domain_model_signup a,
        // 			fe_users b where a.useruid=b.uid and a.deleted=0 and a.hidden=0 and
        // 		    b.deleted=0 ".$strWhere." group by b.community ) d
        // 			on c.uid=d.community where deleted=0 and dicttype=4 order by sort asc";
        // $list = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows("*", "(".$couSql.") tab", "","","");
        
        // //统计总数
        // $sumall="select sum(tnum) tallnum from
        // 		(SELECT b.community,count(1) tnum FROM  tx_activity_domain_model_signup a,fe_users b
        // 		where a.useruid=b.uid and a.deleted=0 and a.hidden=0 and b.deleted=0 ".$strWhere." group by b.community ) tab";
        // $info = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow("*","(".$sumall.") t","");
        
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_activity_domain_model_signup');
        $records = $queryBuilder
        ->add('select','tab2.community,count(1) tnum')
        ->from('tx_activity_domain_model_signup','tab1')
        ->join(
            'tab1',
            'tx_activity_domain_model_volunteer',
            'tab2',
            $queryBuilder->expr()->eq('tab2.uid', $queryBuilder->quoteIdentifier('tab1.volunteer'))
        )
        ->where($strWhere)
        ->groupBy('tab2.community')
        ->execute()
        ->fetchAll();
        return $records;
    }
}
