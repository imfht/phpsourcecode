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

namespace Customer\Entity;

use Admin\Entity\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * 供应商分类
 * @package Customer\Entity
 * @ORM\Entity(repositoryClass="Customer\Repository\SupplierCategoryRepository")
 * @ORM\Table(name="dberp_supplier_category")
 */
class SupplierCategory extends BaseEntity
{
    /**
     * 供应商分类
     * @ORM\Id()
     * @ORM\Column(name="supplier_category_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $supplierCategoryId;

    /**
     * 供应商分类编号
     * @ORM\Column(name="supplier_category_code", type="string", length=20)
     */
    private $supplierCategoryCode;

    /**
     * 供应商分类名称
     * @ORM\Column(name="supplier_category_name", type="string", length=100)
     */
    private $supplierCategoryName;

    /**
     * 供应商分类排序
     * @ORM\Column(name="supplier_category_sort", type="integer", length=11)
     */
    private $supplierCategorySort;

    /**
     * 管理员id
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * 连接供应商
     * @ORM\OneToMany(targetEntity="Customer\Entity\Supplier", mappedBy="supplierCategory")
     * @ORM\JoinColumn(name="supplier_category_id", referencedColumnName="supplier_category_id")
     */
    private $suppliers;

    public function __construct()
    {
        $this->suppliers = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getSuppliers()
    {
        return $this->suppliers;
    }

    public function addSuppliers($supplier)
    {
        $this->suppliers[] = $supplier;
    }

    /**
     * @return mixed
     */
    public function getSupplierCategoryId()
    {
        return $this->supplierCategoryId;
    }

    /**
     * @param mixed $supplierCategoryId
     */
    public function setSupplierCategoryId($supplierCategoryId)
    {
        $this->supplierCategoryId = $supplierCategoryId;
    }

    /**
     * @return mixed
     */
    public function getSupplierCategoryCode()
    {
        return $this->supplierCategoryCode;
    }

    /**
     * @param mixed $supplierCategoryCode
     */
    public function setSupplierCategoryCode($supplierCategoryCode)
    {
        $this->supplierCategoryCode = $supplierCategoryCode;
    }

    /**
     * @return mixed
     */
    public function getSupplierCategoryName()
    {
        return $this->supplierCategoryName;
    }

    /**
     * @param mixed $supplierCategoryName
     */
    public function setSupplierCategoryName($supplierCategoryName)
    {
        $this->supplierCategoryName = $supplierCategoryName;
    }

    /**
     * @return mixed
     */
    public function getSupplierCategorySort()
    {
        return $this->supplierCategorySort;
    }

    /**
     * @param mixed $supplierCategorySort
     */
    public function setSupplierCategorySort($supplierCategorySort)
    {
        $this->supplierCategorySort = $supplierCategorySort;
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