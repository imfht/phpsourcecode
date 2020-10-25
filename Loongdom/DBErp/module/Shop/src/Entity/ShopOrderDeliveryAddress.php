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

namespace Shop\Entity;

use Admin\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * 商城订单配送地址
 * Class ShopOrderDeliveryAddress
 * @package Shop\Entity
 * @ORM\Entity(repositoryClass="Shop\Repository\ShopOrderDeliveryAddressRepository")
 * @ORM\Table(name="dberp_shop_order_delivery_address")
 */
class ShopOrderDeliveryAddress
{
    /**
     * 自增id
     * @ORM\Id()
     * @ORM\Column(name="delivery_address_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $deliveryAddressId;

    /**
     * 收货人名称
     * @ORM\Column(name="delivery_name", type="string", length=100)
     */
    private $deliveryName;

    /**
     * 省市名称
     * @ORM\Column(name="region_info", type="string", length=50)
     */
    private $regionInfo;

    /**
     * 详细地址
     * @ORM\Column(name="region_address", type="string", length=300)
     */
    private $regionAddress;

    /**
     * 邮政编码
     * @ORM\Column(name="zip_code", type="string", length=10)
     */
    private $zipCode;

    /**
     * 手机号码
     * @ORM\Column(name="delivery_phone", type="string", length=20)
     */
    private $deliveryPhone;

    /**
     * 座机号码
     * @ORM\Column(name="delivery_telephone", type="string", length=20)
     */
    private $deliveryTelephone;

    /**
     * 配送地址备注信息
     * @ORM\Column(name="delivery_info", type="string", length=500)
     */
    private $deliveryInfo;

    /**
     * 快递单号
     * @ORM\Column(name="delivery_number", type="string", length=30)
     */
    private $deliveryNumber;

    /**
     * 订单id
     * @ORM\Column(name="shop_order_id", type="integer", length=11)
     */
    private $shopOrderId;

    /**
     * @return mixed
     */
    public function getDeliveryAddressId()
    {
        return $this->deliveryAddressId;
    }

    /**
     * @param mixed $deliveryAddressId
     */
    public function setDeliveryAddressId($deliveryAddressId)
    {
        $this->deliveryAddressId = $deliveryAddressId;
    }

    /**
     * @return mixed
     */
    public function getDeliveryName()
    {
        return $this->deliveryName;
    }

    /**
     * @param mixed $deliveryName
     */
    public function setDeliveryName($deliveryName)
    {
        $this->deliveryName = $deliveryName;
    }

    /**
     * @return mixed
     */
    public function getRegionInfo()
    {
        return $this->regionInfo;
    }

    /**
     * @param mixed $regionInfo
     */
    public function setRegionInfo($regionInfo)
    {
        $this->regionInfo = $regionInfo;
    }

    /**
     * @return mixed
     */
    public function getRegionAddress()
    {
        return $this->regionAddress;
    }

    /**
     * @param mixed $regionAddress
     */
    public function setRegionAddress($regionAddress)
    {
        $this->regionAddress = $regionAddress;
    }

    /**
     * @return mixed
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param mixed $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return mixed
     */
    public function getDeliveryPhone()
    {
        return $this->deliveryPhone;
    }

    /**
     * @param mixed $deliveryPhone
     */
    public function setDeliveryPhone($deliveryPhone)
    {
        $this->deliveryPhone = $deliveryPhone;
    }

    /**
     * @return mixed
     */
    public function getDeliveryTelephone()
    {
        return $this->deliveryTelephone;
    }

    /**
     * @param mixed $deliveryTelephone
     */
    public function setDeliveryTelephone($deliveryTelephone)
    {
        $this->deliveryTelephone = $deliveryTelephone;
    }

    /**
     * @return mixed
     */
    public function getDeliveryInfo()
    {
        return $this->deliveryInfo;
    }

    /**
     * @param mixed $deliveryInfo
     */
    public function setDeliveryInfo($deliveryInfo)
    {
        $this->deliveryInfo = $deliveryInfo;
    }

    /**
     * @return mixed
     */
    public function getShopOrderId()
    {
        return $this->shopOrderId;
    }

    /**
     * @return mixed
     */
    public function getDeliveryNumber()
    {
        return $this->deliveryNumber;
    }

    /**
     * @param mixed $deliveryNumber
     */
    public function setDeliveryNumber($deliveryNumber)
    {
        $this->deliveryNumber = $deliveryNumber;
    }

    /**
     * @param mixed $shopOrderId
     */
    public function setShopOrderId($shopOrderId)
    {
        $this->shopOrderId = $shopOrderId;
    }

}