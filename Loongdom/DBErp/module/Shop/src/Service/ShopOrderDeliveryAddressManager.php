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

namespace Shop\Service;

use Doctrine\ORM\EntityManager;
use Shop\Entity\ShopOrderDeliveryAddress;

class ShopOrderDeliveryAddressManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加订单配送地址
     * @param array $data
     * @return ShopOrderDeliveryAddress
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addShopOrderDeliveryAddress(array $data, int $shopOrderId)
    {
        $shopOrderAddress = new ShopOrderDeliveryAddress();
        $shopOrderAddress->setDeliveryAddressId(null);
        $shopOrderAddress->setShopOrderId($shopOrderId);
        $shopOrderAddress->setDeliveryName($data['delivery_name']);
        $shopOrderAddress->setRegionInfo($data['region_info']);
        $shopOrderAddress->setRegionAddress($data['region_address']);
        $shopOrderAddress->setZipCode($data['zip_code']);
        $shopOrderAddress->setDeliveryPhone($data['delivery_phone']);
        $shopOrderAddress->setDeliveryTelephone($data['delivery_telephone']);
        $shopOrderAddress->setDeliveryInfo($data['delivery_info']);

        $this->entityManager->persist($shopOrderAddress);
        $this->entityManager->flush();

        return $shopOrderAddress;
    }

    /**
     * 添加快递单号
     * @param $number
     * @param $shopOrderId
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addShopOrderDeliveryNumber($number, $shopOrderId)
    {
        $deliveryAddress = $this->entityManager->getRepository(ShopOrderDeliveryAddress::class)->findOneByShopOrderId($shopOrderId);
        $deliveryAddress->setDeliveryNumber($number);
        $this->entityManager->flush();
    }

    /**
     * 删除订单的收货地址
     * @param int $shopOrderId
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteShopOrderDeliveryAddress(int $shopOrderId)
    {
        $addressInfo = $this->entityManager->getRepository(ShopOrderDeliveryAddress::class)->findOneByShopOrderId($shopOrderId);
        if($addressInfo) {
            $this->entityManager->remove($addressInfo);
            $this->entityManager->flush();
        }
        return true;
    }
}