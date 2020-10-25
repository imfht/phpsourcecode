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

namespace Sales\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sales\Entity\SalesOrderReturn;

class SalesOrderReturnRepository extends EntityRepository
{
    /**
     * 销售退货单sql
     * @param array $search
     * @return \Doctrine\ORM\Query
     */
    public function findAllSalesOrderReturn($search = [])
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('s', 'o')
            ->from(SalesOrderReturn::class, 's')
            ->join('s.oneSalesOrder', 'o')
            ->orderBy('s.returnState', 'ASC')
            ->addOrderBy('s.salesOrderReturnId', 'DESC');

        $query = $this->querySearchData($search, $query);

        return $query->getQuery();
    }

    private function querySearchData($search, QueryBuilder $queryBuilder)
    {
        if(isset($search['start_amount']) && $search['start_amount'] > 0)               $queryBuilder->andWhere($queryBuilder->expr()->gte('s.salesOrderReturnAmount', $search['start_amount']));
        if(isset($search['end_amount']) && $search['end_amount'] > 0)                   $queryBuilder->andWhere($queryBuilder->expr()->lte('s.salesOrderReturnAmount', $search['end_amount']));
        if(isset($search['sales_order_sn']) && !empty($search['sales_order_sn']))       $queryBuilder->andWhere($queryBuilder->expr()->like('s.salesOrderSn', "'%".$search['sales_order_sn']."%'"));
        if(isset($search['sales_send_order_sn']) && !empty($search['sales_send_order_sn']))       $queryBuilder->andWhere($queryBuilder->expr()->like('s.salesSendOrderSn', "'%".$search['sales_send_order_sn']."%'"));
        if(isset($search['customer_contacts']) && !empty($search['customer_contacts'])) $queryBuilder->andWhere($queryBuilder->expr()->like('o.customerContacts', "'%".$search['customer_contacts']."%'"));
        if(isset($search['customer_phone']) && !empty($search['customer_phone']))       $queryBuilder->andWhere($queryBuilder->expr()->like('o.customerPhone', "'%".$search['customer_phone']."%'"));
        if(isset($search['customer_id']) && $search['customer_id'] > 0)                 $queryBuilder->andWhere($queryBuilder->expr()->eq('o.customerId', $search['customer_id']));
        if(isset($search['return_state']) && !empty($search['return_state']))           $queryBuilder->andWhere($queryBuilder->expr()->eq('s.returnState', $search['return_state']));

        return $queryBuilder;
    }
}