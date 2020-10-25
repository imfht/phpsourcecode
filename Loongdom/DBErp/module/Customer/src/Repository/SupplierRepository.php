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

use Customer\Entity\Supplier;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class SupplierRepository extends EntityRepository
{
    /**
     * 供应商列表
     * @param array $search
     * @return \Doctrine\ORM\Query
     */
    public function findAllSupplier($search = [])
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('s', 'c')
            ->from(Supplier::class, 's')
            ->join('s.supplierCategory', 'c')
            ->orderBy('s.supplierSort', 'ASC');

        $query = $this->querySearchData($search, $query);

        return $query->getQuery();
    }

    private function querySearchData($search, QueryBuilder $queryBuilder)
    {
        if(isset($search['start_id']) && $search['start_id'] > 0)                           $queryBuilder->andWhere($queryBuilder->expr()->gte('s.supplierId', $search['start_id']));
        if(isset($search['end_id']) && $search['end_id'] > 0)                               $queryBuilder->andWhere($queryBuilder->expr()->lte('s.supplierId', $search['end_id']));
        if(isset($search['supplier_category_id']) && $search['supplier_category_id'] > 0)   $queryBuilder->andWhere($queryBuilder->expr()->eq('s.supplierCategoryId', $search['supplier_category_id']));
        if(isset($search['supplier_name']) && !empty($search['supplier_name']))             $queryBuilder->andWhere($queryBuilder->expr()->like('s.supplierName', "'%".$search['supplier_name']."%'"));
        if(isset($search['supplier_contacts']) && !empty($search['supplier_contacts']))     $queryBuilder->andWhere($queryBuilder->expr()->like('s.supplierContacts', "'%".$search['supplier_contacts']."%'"));
        if(isset($search['supplier_code']) && !empty($search['supplier_code']))             $queryBuilder->andWhere($queryBuilder->expr()->like('s.supplierCode', "'%".$search['supplier_code']."%'"));
        if(isset($search['supplier_phone']) && !empty($search['supplier_phone'])) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('s.supplierPhone', "'%".$search['supplier_phone']."%'"),
                    $queryBuilder->expr()->like('s.supplierTelephone', "'%".$search['supplier_phone']."%'")
                )
            );
        }

        return $queryBuilder;
    }
}