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
use Admin\View\Helper\ErpCurrencyFormatHelper;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Goods
 * @package Store\Entity
 * @ORM\Entity(repositoryClass="Store\Repository\GoodsRepository")
 * @ORM\Table(name="dberp_goods")
 */
class Goods extends BaseEntity
{
    /**
     * @var
     * @ORM\Id()
     * @ORM\Column(name="goods_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $goodsId;

    /**
     * 商品分类Id
     * @ORM\Column(name="goods_category_id", type="integer", length=11)
     */
    private $goodsCategoryId;

    /**
     * 商品品牌id
     * @ORM\Column(name="brand_id", type="integer", length=11)
     */
    private $brandId;

    /**
     * 商品名称
     * @ORM\Column(name="goods_name", type="string", length=100)
     */
    private $goodsName;

    /**
     * 商品库存
     * @ORM\Column(name="goods_stock", type="integer", length=11)
     */
    private $goodsStock;

    /**
     * 商品价格
     * @ORM\Column(name="goods_price", type="decimal", scale=4)
     */
    private $goodsPrice;

    /**
     * 建议售价
     * @ORM\Column(name="goods_recommend_price", type="decimal", scale=4)
     */
    private $goodsRecommendPrice;

    /**
     * 商品规格
     * @ORM\Column(name="goods_spec", type="string", length=100)
     */
    private $goodsSpec;

    /**
     * 商品编号
     * @ORM\Column(name="goods_number", type="string", length=30)
     */
    private $goodsNumber;

    /**
     * 计量单位Id
     * @ORM\Column(name="unit_id", type="integer", length=11)
     */
    private $unitId;

    /**
     * 条形码
     * @ORM\Column(name="goods_barcode", type="string", length=30)
     */
    private $goodsBarcode;

    /**
     * 商品备注
     * @ORM\Column(name="goods_info", type="string", length=500)
     */
    private $goodsInfo;

    /**
     * 商品排序
     * @ORM\Column(name="goods_sort", type="integer", length=11)
     */
    private $goodsSort;

    /**
     * 管理员Id
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * 分类
     * @ORM\ManyToOne(targetEntity="Store\Entity\GoodsCategory", inversedBy="goods")
     * @ORM\JoinColumn(name="goods_category_id", referencedColumnName="goods_category_id")
     */
    private $goodsCategory;

    /**
     * 品牌
     * @ORM\ManyToOne(targetEntity="Store\Entity\Brand", inversedBy="goods")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="brand_id")
     */
    private $brand;

    /**
     * @var
     * @ORM\OneToOne(targetEntity="Store\Entity\Unit")
     * @ORM\JoinColumn(name="unit_id", referencedColumnName="unit_id")
     */
    private $oneUnit;

    /**
     * @return mixed
     */
    public function getOneUnit()
    {
        return $this->oneUnit;
    }

    /**
     * @param mixed $oneUnit
     */
    public function setOneUnit($oneUnit)
    {
        $this->oneUnit = $oneUnit;
    }

    /**
     * @return mixed
     */
    public function getGoodsCategory()
    {
        return $this->goodsCategory;
    }

    /**
     * @param mixed $goodsCategory
     */
    public function setGoodsCategory(GoodsCategory $goodsCategory)
    {
        $this->goodsCategory = $goodsCategory;
        $goodsCategory->addGoods($this);
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param mixed $brand
     */
    public function setBrand(Brand $brand)
    {
        $this->brand = $brand;
        $brand->addGoods($this);
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
    public function getGoodsCategoryId()
    {
        return $this->goodsCategoryId;
    }

    /**
     * @param mixed $goodsCategoryId
     */
    public function setGoodsCategoryId($goodsCategoryId)
    {
        $this->goodsCategoryId = $goodsCategoryId;
    }

    /**
     * @return mixed
     */
    public function getBrandId()
    {
        return $this->brandId;
    }

    /**
     * @param mixed $brandId
     */
    public function setBrandId($brandId)
    {
        $this->brandId = $brandId;
    }

    /**
     * @return mixed
     */
    public function getGoodsName()
    {
        return $this->goodsName;
    }

    /**
     * @param mixed $goodsName
     */
    public function setGoodsName($goodsName)
    {
        $this->goodsName = $goodsName;
    }

    /**
     * @return mixed
     */
    public function getGoodsStock()
    {
        return $this->goodsStock;
    }

    /**
     * @param mixed $goodsStock
     */
    public function setGoodsStock($goodsStock)
    {
        $this->goodsStock = $goodsStock;
    }

    /**
     * @return mixed
     */
    public function getGoodsPrice()
    {
        return $this->goodsPrice;
    }

    /**
     * @param mixed $goodsPrice
     */
    public function setGoodsPrice($goodsPrice)
    {
        $this->goodsPrice = $goodsPrice;
    }

    /**
     * @return mixed
     */
    public function getGoodsRecommendPrice()
    {
        return $this->goodsRecommendPrice;
    }

    /**
     * @param mixed $goodsRecommendPrice
     */
    public function setGoodsRecommendPrice($goodsRecommendPrice)
    {
        $this->goodsRecommendPrice = $goodsRecommendPrice;
    }

    /**
     * @return mixed
     */
    public function getGoodsSpec()
    {
        return $this->goodsSpec;
    }

    /**
     * @param mixed $goodsSpec
     */
    public function setGoodsSpec($goodsSpec)
    {
        $this->goodsSpec = $goodsSpec;
    }

    /**
     * @return mixed
     */
    public function getGoodsNumber()
    {
        return $this->goodsNumber;
    }

    /**
     * @param mixed $goodsNumber
     */
    public function setGoodsNumber($goodsNumber)
    {
        $this->goodsNumber = $goodsNumber;
    }

    /**
     * @return mixed
     */
    public function getUnitId()
    {
        return $this->unitId;
    }

    /**
     * @param mixed $unitId
     */
    public function setUnitId($unitId)
    {
        $this->unitId = $unitId;
    }

    /**
     * @return mixed
     */
    public function getGoodsBarcode()
    {
        return $this->goodsBarcode;
    }

    /**
     * @param mixed $goodsBarcode
     */
    public function setGoodsBarcode($goodsBarcode)
    {
        $this->goodsBarcode = $goodsBarcode;
    }

    /**
     * @return mixed
     */
    public function getGoodsInfo()
    {
        return $this->goodsInfo;
    }

    /**
     * @param mixed $goodsInfo
     */
    public function setGoodsInfo($goodsInfo)
    {
        $this->goodsInfo = $goodsInfo;
    }

    /**
     * @return mixed
     */
    public function getGoodsSort()
    {
        return $this->goodsSort;
    }

    /**
     * @param mixed $goodsSort
     */
    public function setGoodsSort($goodsSort)
    {
        $this->goodsSort = $goodsSort;
    }

    /**
     * @return mixed
     */
    public function getAdminId()
    {
        return $this->adminId;
    }

    /**
     * @param mixed $adminId
     */
    public function setAdminId($adminId)
    {
        $this->adminId = $adminId;
    }

    public function goodsValuesArray()
    {
        $array = $this->valuesArray();
        $array['unitName'] = $this->getOneUnit()->getUnitName();

        return $array;
    }
}