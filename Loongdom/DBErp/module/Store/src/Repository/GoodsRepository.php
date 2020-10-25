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

namespace Store\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Store\Entity\Goods;

class GoodsRepository extends EntityRepository
{
    /**
     * 抛出获取商品的sql
     * @param array $search
     * @return \Doctrine\ORM\Query
     */
    public function findAllGoods($search = [])
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('g', 'c', 'b')
            //->addSelect('(SELECT SUM(w.warehouseGoodsStock) FROM Store\Entity\WarehouseGoods w WHERE w.goodsId = g.goodsId) AS warehouse_goods_num')
            ->from(Goods::class, 'g')
            ->join('g.goodsCategory', 'c')
            ->leftJoin('g.brand', 'b')
            ->orderBy('g.goodsSort', 'ASC')
            ->addOrderBy('g.goodsId', 'DESC');

        $query = $this->querySearchData($search, $query);

        return $query->getQuery();
    }

    /**
     * 检索商品名称
     * @param $goodsName
     * @return mixed
     */
    public function findGoodsNameSearch($goodsName)
    {
        //当定义为某个字段时，输出的是数组
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('g.goodsId,g.goodsName,g.goodsSpec')
            ->from(Goods::class, 'g')
            ->where('g.goodsName LIKE \'%'.$goodsName.'%\'')
            ->setMaxResults(10);

        $goodsResult = $query->getQuery()->getResult();
        return $goodsResult;
    }

    private function querySearchData($search, QueryBuilder $queryBuilder)
    {
        if(isset($search['start_id']) && $search['start_id'] > 0)                   $queryBuilder->andWhere($queryBuilder->expr()->gte('g.goodsId', $search['start_id']));
        if(isset($search['end_id']) && $search['end_id'] > 0)                       $queryBuilder->andWhere($queryBuilder->expr()->lte('g.goodsId', $search['end_id']));
        if(isset($search['start_price']) && $search['start_price'] > 0)             $queryBuilder->andWhere($queryBuilder->expr()->gte('g.goodsPrice', $search['start_price']));
        if(isset($search['end_price']) && $search['end_price'] > 0)                 $queryBuilder->andWhere($queryBuilder->expr()->lte('g.goodsPrice', $search['end_price']));
        if(isset($search['start_sales_price']) && $search['start_sales_price'] > 0) $queryBuilder->andWhere($queryBuilder->expr()->gte('g.goodsRecommendPrice', $search['start_sales_price']));
        if(isset($search['end_sales_price']) && $search['end_sales_price'] > 0)     $queryBuilder->andWhere($queryBuilder->expr()->lte('g.goodsRecommendPrice', $search['end_sales_price']));
        if(isset($search['start_stock']) && $search['start_stock'] > 0)             $queryBuilder->andWhere($queryBuilder->expr()->gte('g.goodsStock', $search['start_stock']));
        if(isset($search['end_stock']) && $search['end_stock'] > 0)                 $queryBuilder->andWhere($queryBuilder->expr()->lte('g.goodsStock', $search['end_stock']));
        if(isset($search['goods_category_id']) && $search['goods_category_id'] > 0) $queryBuilder->andWhere($queryBuilder->expr()->eq('g.goodsCategoryId', $search['goods_category_id']));
        if(isset($search['brand_id']) && $search['brand_id'] > 0)                   $queryBuilder->andWhere($queryBuilder->expr()->eq('g.brandId', $search['brand_id']));

        if(isset($search['goods_name']) && !empty($search['goods_name']))           $queryBuilder->andWhere($queryBuilder->expr()->like('g.goodsName', "'%".$search['goods_name']."%'"));
        if(isset($search['goods_number']) && !empty($search['goods_number']))       $queryBuilder->andWhere($queryBuilder->expr()->like('g.goodsNumber', "'%".$search['goods_number']."%'"));
        if(isset($search['goods_spec']) && !empty($search['goods_spec']))           $queryBuilder->andWhere($queryBuilder->expr()->like('g.goodsSpec', "'%".$search['goods_spec']."%'"));

        return $queryBuilder;
    }
}