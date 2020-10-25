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

namespace Customer\Repository;

use Customer\Entity\Customer;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class CustomerRepository extends EntityRepository
{
    /**
     * 客户列表
     * @param array $search
     * @return \Doctrine\ORM\Query
     */
    public function findAllCustomer($search = [])
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('c', 't')
            ->from(Customer::class, 'c')
            ->join('c.customerCategory', 't')
            ->orderBy('c.customerSort', 'ASC');

        $query = $this->querySearchData($search, $query);

        return $query->getQuery();
    }

    private function querySearchData($search, QueryBuilder $queryBuilder)
    {
        if(isset($search['start_id']) && $search['start_id'] > 0)                           $queryBuilder->andWhere($queryBuilder->expr()->gte('c.customerId', $search['start_id']));
        if(isset($search['end_id']) && $search['end_id'] > 0)                               $queryBuilder->andWhere($queryBuilder->expr()->lte('c.customerId', $search['end_id']));
        if(isset($search['customer_category_id']) && $search['customer_category_id'] > 0)   $queryBuilder->andWhere($queryBuilder->expr()->eq('c.customerCategoryId', $search['customer_category_id']));
        if(isset($search['customer_name']) && !empty($search['customer_name']))             $queryBuilder->andWhere($queryBuilder->expr()->like('c.customerName', "'%".$search['customer_name']."%'"));
        if(isset($search['customer_code']) && !empty($search['customer_code']))             $queryBuilder->andWhere($queryBuilder->expr()->like('c.customerCode', "'%".$search['customer_code']."%'"));

        return $queryBuilder;
    }
}