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

namespace Store\Entity;

use Admin\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * 仓库商品
 * Class WarehouseGoods
 * @package Store\Entity
 * @ORM\Entity(repositoryClass="Store\Repository\WarehouseGoodsRepository")
 * @ORM\Table(name="dberp_warehouse_goods")
 */
class WarehouseGoods extends BaseEntity
{
    /**
     * 仓库商品id
     * @ORM\Id()
     * @ORM\Column(name="warehouse_goods_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $warehouseGoodsId;

    /**
     * 仓库id
     * @ORM\Column(name="warehouse_id", type="integer", length=11)
     */
    private $warehouseId;

    /**
     * 商品id
     * @ORM\Column(name="goods_id", type="integer", length=11)
     */
    private $goodsId;

    /**
     * 商品库存
     * @ORM\Column(name="warehouse_goods_stock", type="integer",length=11)
     */
    private $warehouseGoodsStock;

    /**
     * 仓库信息
     * @ORM\OneToOne(targetEntity="Store\Entity\Warehouse")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="warehouse_id")
     */
    private $oneWarehouse;

    /**
     * @return mixed
     */
    public function getOneWarehouse()
    {
        return $this->oneWarehouse;
    }

    /**
     * @param mixed $oneWarehouse
     */
    public function setOneWarehouse($oneWarehouse)
    {
        $this->oneWarehouse = $oneWarehouse;
    }

    /**
     * @return mixed
     */
    public function getWarehouseGoodsId()
    {
        return $this->warehouseGoodsId;
    }

    /**
     * @param mixed $warehouseGoodsId
     */
    public function setWarehouseGoodsId($warehouseGoodsId)
    {
        $this->warehouseGoodsId = $warehouseGoodsId;
    }

    /**
     * @return mixed
     */
    public function getWarehouseId()
    {
        return $this->warehouseId;
    }

    /**
     * @param mixed $warehouseId
     */
    public function setWarehouseId($warehouseId)
    {
        $this->warehouseId = $warehouseId;
    }

    /**
     * @return mixed
     */
    public function getGoodsId()
    {
        return $this->goodsId;
    }

    /**
     * @param mixed $goodsId
     */
    public function setGoodsId($goodsId)
    {
        $this->goodsId = $goodsId;
    }

    /**
     * @return mixed
     */
    public function getWarehouseGoodsStock()
    {
        return $this->warehouseGoodsStock;
    }

    /**
     * @param mixed $warehouseGoodsStock
     */
    public function setWarehouseGoodsStock($warehouseGoodsStock)
    {
        $this->warehouseGoodsStock = $warehouseGoodsStock;
    }

}