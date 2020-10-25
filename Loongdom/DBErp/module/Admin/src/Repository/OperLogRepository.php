<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright 北京珑大钜商科技有限公司，并保留所有权利。
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    静静的风 <baron@loongdom.cn>
 *
 */

namespace Admin\Repository;

use Admin\Entity\OperLog;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class OperLogRepository extends EntityRepository
{
    /**
     * 获取操作日志列表sql
     * @param array $search
     * @return \Doctrine\ORM\Query
     */
    public function findOperLogAll($search = [])
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('o')
            ->from(OperLog::class, 'o')
            ->orderBy('o.logId', 'DESC');

        $query = $this->querySearchData($search, $query);

        return $query->getQuery();
    }

    /**
     * 对检索信息进行处理
     * @param $search
     * @param QueryBuilder $query
     * @return QueryBuilder
     */
    private function querySearchData($search, QueryBuilder $query)
    {
        if(isset($search['log_oper_user']) && !empty($search['log_oper_user']))             $query->andWhere($query->expr()->like('o.logOperUser', "'%".$search['log_oper_user']."%'"));
        if(isset($search['log_ip']) && !empty($search['log_ip']))                           $query->andWhere($query->expr()->like('o.logIp', "'%".$search['log_ip']."%'"));
        if(isset($search['log_body']) && !empty($search['log_body']))                       $query->andWhere($query->expr()->like('o.logBody', "'%".$search['log_body']."%'"));
        if(isset($search['log_oper_user_group']) && !empty($search['log_oper_user_group'])) $query->andWhere($query->expr()->eq('o.logOperUserGroup', ':logOperUserGroup'))->setParameter('logOperUserGroup', $search['log_oper_user_group']);
        if(isset($search['start_time']) && !empty($search['start_time']))                   $query->andWhere($query->expr()->gte('o.logTime', ':startTime'))->setParameter('startTime', strtotime($search['start_time']));
        if(isset($search['end_time']) && !empty($search['end_time']))                       $query->andWhere($query->expr()->lte('o.logTime', ':endTime'))->setParameter('endTime', strtotime($search['end_time']));

        return $query;
    }
}