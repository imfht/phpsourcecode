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

namespace Purchase\Service;

use Doctrine\ORM\EntityManager;
use Purchase\Entity\PurchaseGoodsPriceLog;

class PurchaseGoodsPriceLogManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加采购商品价格记录
     * @param array $data
     * @param int $pOrderId
     * @param $insertTime
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addPurchaseGoodsPriceLog(array $data, int $pOrderId, $insertTime)
    {
        if(empty($data)) return false;
        foreach ($data as $goodsValue) {
            $purchasePriceLog = new PurchaseGoodsPriceLog();
            $purchasePriceLog->setPriceLogId(null);
            $purchasePriceLog->setPOrderId($pOrderId);
            $purchasePriceLog->setGoodsPrice($goodsValue->getPGoodsPrice());
            $purchasePriceLog->setGoodsId($goodsValue->getGoodsId());
            $purchasePriceLog->setLogTime($insertTime);

            $this->entityManager->persist($purchasePriceLog);
            $this->entityManager->flush();
        }

        return true;
    }
}