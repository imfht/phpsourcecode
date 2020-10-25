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

use Doctrine\ORM\EntityRepository;
use Admin\Entity\AdminUser;
use Doctrine\ORM\QueryBuilder;

class AdminUserRepository extends EntityRepository
{
    /**
     * 获取管理员列表sql
     * @param array $search
     * @return \Doctrine\ORM\Query
     */
    public function findAllAdmin($search = [])
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('a', 'g')
            ->from(AdminUser::class, 'a')
            ->join('a.group', 'g')
            ->orderBy('a.adminId', 'ASC');

        //检索
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
        if(isset($search['start_id']) && $search['start_id'] > 0)               $query->andWhere($query->expr()->gte('a.adminId', $search['start_id']));
        if(isset($search['end_id']) && $search['end_id'] > 0)                   $query->andWhere($query->expr()->lte('a.adminId', $search['end_id']));
        if(isset($search['admin_name']) && !empty($search['admin_name']))       $query->andWhere($query->expr()->like('a.adminName', "'%".$search['admin_name']."%'"));
        if(isset($search['admin_email']) && !empty($search['admin_email']))     $query->andWhere($query->expr()->eq('a.adminEmail', ':adminEmail'))->setParameter('adminEmail', $search['admin_email']);
        if(isset($search['admin_group_id']) && $search['admin_group_id'] > 0)   $query->andWhere($query->expr()->eq('a.adminGroupId', $search['admin_group_id']));
        if(isset($search['admin_state']) && is_numeric($search['admin_state'])) $query->andWhere($query->expr()->eq('a.adminState', $search['admin_state']));
        if(isset($search['start_time']) && !empty($search['start_time']))       $query->andWhere($query->expr()->gte('a.adminAddTime', ':startTime'))->setParameter('startTime', strtotime($search['start_time']));
        if(isset($search['end_time']) && !empty($search['end_time']))           $query->andWhere($query->expr()->lte('a.adminAddTime', ':endTime'))->setParameter('endTime', strtotime($search['end_time']));

        return $query;
    }
}