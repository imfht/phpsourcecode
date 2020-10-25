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
use Sales\Entity\SalesSendOrder;

class SalesSendOrderRepository extends EntityRepository
{
    /**
     * 发货订单sql
     * @param array $search
     * @return \Doctrine\ORM\Query
     */
    public function findAllSendOrder($search = [])
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('s', 'o')
            ->from(SalesSendOrder::class, 's')
            ->join('s.oneSalesOrder', 'o')
            ->orderBy('o.salesOrderState', 'ASC')
            ->addOrderBy('s.sendOrderId', 'DESC');

        $query = $this->querySearchData($search, $query);

        return $query->getQuery();
    }

    private function querySearchData($search, QueryBuilder $queryBuilder)
    {
        if(isset($search['start_amount']) && $search['start_amount'] > 0)               $queryBuilder->andWhere($queryBuilder->expr()->gte('o.salesOrderAmount', $search['start_amount']));
        if(isset($search['end_amount']) && $search['end_amount'] > 0)                   $queryBuilder->andWhere($queryBuilder->expr()->lte('o.salesOrderAmount', $search['end_amount']));
        if(isset($search['send_order_sn']) && !empty($search['send_order_sn']))       $queryBuilder->andWhere($queryBuilder->expr()->like('s.sendOrderSn', "'%".$search['send_order_sn']."%'"));
        if(isset($search['customer_contacts']) && !empty($search['customer_contacts'])) $queryBuilder->andWhere($queryBuilder->expr()->like('o.customerContacts', "'%".$search['customer_contacts']."%'"));
        if(isset($search['customer_phone']) && !empty($search['customer_phone']))       $queryBuilder->andWhere($queryBuilder->expr()->like('o.customerPhone', "'%".$search['customer_phone']."%'"));
        if(isset($search['customer_id']) && $search['customer_id'] > 0)                 $queryBuilder->andWhere($queryBuilder->expr()->eq('o.customerId', $search['customer_id']));
        if(isset($search['receivables_code']) && !empty($search['receivables_code']))   $queryBuilder->andWhere($queryBuilder->expr()->eq('o.receivablesCode', ':code'))->setParameter('code', $search['receivables_code']);
        if(isset($search['sales_order_state']) && is_numeric($search['sales_order_state'])) $queryBuilder->andWhere($queryBuilder->expr()->eq('o.salesOrderState', $search['sales_order_state']));
        if(isset($search['return_state']) && is_numeric($search['return_state']))           $queryBuilder->andWhere($queryBuilder->expr()->eq('s.returnState', $search['return_state']));

        return $queryBuilder;
    }
}