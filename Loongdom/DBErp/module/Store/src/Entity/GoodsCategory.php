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
 * Class GoodsCategory
 * @package Store\Entity
 * @ORM\Entity(repositoryClass="Store\Repository\GoodsCategoryRepository")
 * @ORM\Table(name="dberp_goods_category")
 */
class GoodsCategory extends BaseEntity
{
    /**
     * 商品分类ID
     * @ORM\Id()
     * @ORM\Column(name="goods_category_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $goodsCategoryId;

    /**
     * 上级分类id
     * @ORM\Column(name="goods_category_top_id", type="integer", length=11)
     */
    private $goodsCategoryTopId;

    /**
     * 商品分类编码
     * @ORM\Column(name="goods_category_code", type="string", length=30)
     */
    private $goodsCategoryCode;

    /**
     * 商品分类名称
     * @ORM\Column(name="goods_category_name", type="string", length=100)
     */
    private $goodsCategoryName;

    /**
     * 商品分类步长
     * @ORM\Column(name="goods_category_path", type="string", length=255)
     */
    private $goodsCategoryPath;

    /**
     * 商品分类排序
     * @ORM\Column(name="goods_category_sort", type="integer", length=11)
     */
    private $goodsCategorySort;

    /**
     * 管理员id
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * 连接商品表
     * @ORM\OneToMany(targetEntity="Store\Entity\Goods", mappedBy="goodsCategory")
     * @ORM\JoinColumn(name="goods_category_id", referencedColumnName="goods_category_id")
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
    public function getGoodsCategoryTopId()
    {
        return $this->goodsCategoryTopId;
    }

    /**
     * @param mixed $goodsCategoryTopId
     */
    public function setGoodsCategoryTopId($goodsCategoryTopId)
    {
        $this->goodsCategoryTopId = $goodsCategoryTopId;
    }

    /**
     * @return mixed
     */
    public function getGoodsCategoryCode()
    {
        return $this->goodsCategoryCode;
    }

    /**
     * @param mixed $goodsCategoryCode
     */
    public function setGoodsCategoryCode($goodsCategoryCode)
    {
        $this->goodsCategoryCode = $goodsCategoryCode;
    }

    /**
     * @return mixed
     */
    public function getGoodsCategoryName()
    {
        return $this->goodsCategoryName;
    }

    /**
     * @param mixed $goodsCategoryName
     */
    public function setGoodsCategoryName($goodsCategoryName)
    {
        $this->goodsCategoryName = $goodsCategoryName;
    }

    /**
     * @return mixed
     */
    public function getGoodsCategoryPath()
    {
        return $this->goodsCategoryPath;
    }

    /**
     * @param mixed $goodsCategoryPath
     */
    public function setGoodsCategoryPath($goodsCategoryPath)
    {
        $this->goodsCategoryPath = $goodsCategoryPath;
    }

    /**
     * @return mixed
     */
    public function getGoodsCategorySort()
    {
        return $this->goodsCategorySort;
    }

    /**
     * @param mixed $goodsCategorySort
     */
    public function setGoodsCategorySort($goodsCategorySort)
    {
        $this->goodsCategorySort = $goodsCategorySort;
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