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

namespace Store\Service;

use Doctrine\ORM\EntityManager;
use Store\Entity\GoodsCategory;

class GoodsCategoryManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加商品分类
     * @param array $data
     * @param int $adminId
     * @return GoodsCategory
     */
    public function addGoodsCategory(array $data, int $adminId)
    {
        $categoryPath = '';
        if($data['goodsCategoryTopId'] > 0) {
            $topGoodsCategory = $this->entityManager->getRepository(GoodsCategory::class)->findOneByGoodsCategoryId($data['goodsCategoryTopId']);
            if($topGoodsCategory) $categoryPath = $topGoodsCategory->getGoodsCategoryPath();
        }

        $goodsCategory = new GoodsCategory();
        $goodsCategory->valuesSet($data);
        $goodsCategory->setAdminId($adminId);

        $this->entityManager->persist($goodsCategory);
        $this->entityManager->flush();

        $categoryPath = empty($categoryPath) ? $goodsCategory->getGoodsCategoryId() : $categoryPath . ',' . $goodsCategory->getGoodsCategoryId();
        $goodsCategory->setGoodsCategoryPath($categoryPath);
        $this->entityManager->flush();

        return $goodsCategory;
    }

    /**
     * 更新分类
     * @param array $data
     * @param GoodsCategory $goodsCategory
     */
    public function updateGoodsCategory(array $data, GoodsCategory $goodsCategory)
    {
        $categoryPath = '';
        if($data['goodsCategoryTopId'] > 0) {
            $topGoodsCategory = $this->entityManager->getRepository(GoodsCategory::class)->findOneByGoodsCategoryId($data['goodsCategoryTopId']);
            if($topGoodsCategory) $categoryPath = $topGoodsCategory->getGoodsCategoryPath();
        }

        //用于子集进行处理
        $topId      = $goodsCategory->getGoodsCategoryTopId();
        $likeStr    = $goodsCategory->getGoodsCategoryPath() . ',';

        $goodsCategory->valuesSet($data);
        /*$goodsCategory->setGoodsCategoryTopId($data['goods_category_top_id']);
        $goodsCategory->setGoodsCategoryCode($data['goods_category_code']);
        $goodsCategory->setGoodsCategoryName($data['goods_category_name']);
        $goodsCategory->setGoodsCategorySort($data['goods_category_sort']);*/

        $this->entityManager->flush();

        $categoryPath = empty($categoryPath) ? $goodsCategory->getGoodsCategoryId() : $categoryPath . ',' . $goodsCategory->getGoodsCategoryId();
        $goodsCategory->setGoodsCategoryPath($categoryPath);
        $this->entityManager->flush();

        //对子集的处理
        if($topId != $data['goodsCategoryTopId']) {
            $subCategory = $this->entityManager->getRepository(GoodsCategory::class)->findCategorySub($likeStr);
            if($subCategory) {
                foreach ($subCategory as $subValue) {
                    $categoryInfo = $this->entityManager->getRepository(GoodsCategory::class)->findOneByGoodsCategoryId($subValue->getGoodsCategoryId());
                    if($categoryInfo) {
                        $updateStr = str_replace($likeStr, $categoryPath.',', $subValue->getGoodsCategoryPath());
                        $categoryInfo->setGoodsCategoryPath($updateStr);
                        $this->entityManager->flush();
                    }
                }
            }
        }
    }

    /**
     * 批量修改
     * @param array $data
     */
    public function updateAllGoodsCategory(array $data)
    {
        foreach ($data['select_id'] as $value) {
            $category = $this->entityManager->getRepository(GoodsCategory::class)->findOneByGoodsCategoryId($value);

            if($data['editAllState'] == 'sort') {
                $category->setGoodsCategorySort($data['goods_category_sort'][$value]);
            }
            $this->entityManager->flush();
            $this->entityManager->clear(GoodsCategory::class);
        }
    }

    /**
     * 删除分类
     * @param GoodsCategory $goodsCategory
     * @return bool
     */
    public function deleteGoodsCategory(GoodsCategory $goodsCategory)
    {
        $this->entityManager->remove($goodsCategory);
        $this->entityManager->flush();

        return true;
    }
}