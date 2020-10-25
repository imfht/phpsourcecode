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

namespace Sales\Service;

use Doctrine\ORM\EntityManager;
use Sales\Entity\SalesGoodsPriceLog;

class SalesGoodsPriceLogManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加销售商品价格记录
     * @param array $data
     * @param int $salesOrderId
     * @param $insertTime
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addSalesGoodsPriceLog(array $data, int $salesOrderId, $insertTime)
    {
        if(empty($data)) return false;
        foreach ($data as $goodsValue) {
            $salesPriceLog = new SalesGoodsPriceLog();
            $salesPriceLog->setPriceLogId(null);
            $salesPriceLog->setGoodsId($goodsValue->getGoodsId());
            $salesPriceLog->setGoodsPrice($goodsValue->getSalesGoodsPrice());
            $salesPriceLog->setSalesOrderId($salesOrderId);
            $salesPriceLog->setLogTime($insertTime);

            $this->entityManager->persist($salesPriceLog);
            $this->entityManager->flush();
        }
        return true;
    }
}