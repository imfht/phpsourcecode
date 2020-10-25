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
use Store\Entity\Brand;
use Store\Entity\Goods;
use Store\Entity\GoodsCategory;
use Store\Entity\Unit;

class GoodsManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager    = $entityManager;
    }

    /**
     * 添加商品
     * @param array $data
     * @param $adminId
     * @return Goods
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addGoods(array $data, $adminId)
    {
        $goodsCategory = $this->entityManager->getRepository(GoodsCategory::class)->findOneByGoodsCategoryId($data['goodsCategoryId']);
        $unitInfo = $this->entityManager->getRepository(Unit::class)->findOneByUnitId($data['unitId']);
        $brandInfo= $this->entityManager->getRepository(Brand::class)->findOneByBrandId($data['brandId']);
        $goods = new Goods();
        $goods->valuesSet($data);

        $goods->setGoodsStock(0);
        $goods->setAdminId($adminId);
        $goods->setGoodsCategory($goodsCategory);
        $goods->setOneUnit($unitInfo);
        if($brandInfo) $goods->setBrand($brandInfo);

        $this->entityManager->persist($goods);
        $this->entityManager->flush();

        return $goods;
    }

    /**
     * 编辑商品
     * @param array $data
     * @param Goods $goods
     * @return bool
     */
    public function editGoods(array $data, Goods $goods)
    {
        $goods->valuesSet($data);

        $this->entityManager->flush();

        return true;
    }

    /**
     * 更新商品的价格和库存(入库)
     * @param array $data
     * @param Goods $goods
     */
    public function updateGoodsPriceAndStock(array $data, Goods $goods)
    {
        $goods->setGoodsStock($data['goodsStock']);
        $goods->setGoodsPrice($data['goodsPrice']);

        $this->entityManager->flush();
    }

    /**
     * 更新商品的库存
     * @param $stockNum
     * @param Goods $goods
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateGoodsStock($stockNum, Goods $goods)
    {
        $goods->setGoodsStock($stockNum);
        $this->entityManager->flush();
    }

    /**
     * 更新商品的库存(出库)
     * @param array $data
     * @return bool
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function outGoodsStock(array $data)
    {
        if(empty($data)) return false;
        foreach ($data as $goodsId => $sendNum) {
            $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneBy(['goodsId' => $goodsId]);
            if( !$goodsInfo || $goodsInfo->getGoodsStock() < $sendNum) return false;

            $goodsInfo->setGoodsStock($goodsInfo->getGoodsStock() - $sendNum);
            $this->entityManager->flush();
            $this->entityManager->clear(Goods::class);
        }
        return true;
    }

    /**
     * 删除商品
     * @param Goods $goods
     * @return bool
     */
    public function deleteGoods(Goods $goods)
    {
        $this->entityManager->remove($goods);
        $this->entityManager->flush();

        return true;
    }
}