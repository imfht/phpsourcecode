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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Brand
 * @package Store\Entity
 * @ORM\Entity(repositoryClass="Store\Repository\BrandRepository")
 * @ORM\Table(name="dberp_brand")
 */
class Brand extends BaseEntity
{
    /**
     * 品牌id
     * @ORM\Id()
     * @ORM\Column(name="brand_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $brandId;

    /**
     * 品牌名称
     * @ORM\Column(name="brand_name", type="string", length=100)
     */
    private $brandName;

    /**
     * 品牌编码
     * @ORM\Column(name="brand_code", type="string", length=30)
     */
    private $brandCode;

    /**
     * 品牌排序
     * @ORM\Column(name="brand_sort", type="integer", length=11)
     */
    private $brandSort;

    /**
     * 管理员id
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * 商品
     * @ORM\OneToMany(targetEntity="Store\Entity\Goods", mappedBy="brand")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="brand_id")
     */
    private $goods;

    public function __construct()
    {
        $this->goods = new ArrayCollection();
    }

    public function getGoods()
    {
        return $this->goods;
    }
    public function addGoods($goods)
    {
        $this->goods[] = $goods;
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
    public function getBrandName()
    {
        return $this->brandName;
    }

    /**
     * @param mixed $brandName
     */
    public function setBrandName($brandName)
    {
        $this->brandName = $brandName;
    }

    /**
     * @return mixed
     */
    public function getBrandCode()
    {
        return $this->brandCode;
    }

    /**
     * @param mixed $brandCode
     */
    public function setBrandCode($brandCode)
    {
        $this->brandCode = $brandCode;
    }

    /**
     * @return mixed
     */
    public function getBrandSort()
    {
        return $this->brandSort;
    }

    /**
     * @param mixed $brandSort
     */
    public function setBrandSort($brandSort)
    {
        $this->brandSort = $brandSort;
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
}