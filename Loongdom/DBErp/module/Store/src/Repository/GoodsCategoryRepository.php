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
use Store\Entity\GoodsCategory;

class GoodsCategoryRepository extends EntityRepository
{
    /**
     * 查询子分类
     * @param $str
     * @return mixed
     */
    public function findCategorySub($str)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from(GoodsCategory::class, 'c')
            ->where('c.goodsCategoryPath LIKE :word')
            ->setParameter('word', $str.'%')
            ->getQuery()->getResult();
    }
}