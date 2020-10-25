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
 * 供应商
 * @package Customer\Entity
 * @ORM\Entity(repositoryClass="Customer\Repository\SupplierRepository")
 * @ORM\Table(name="dberp_supplier")
 */
class Supplier extends BaseEntity
{
    /**
     * 供应商
     * @ORM\Id()
     * @ORM\Column(name="supplier_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $supplierId;

    /**
     * 供应商分类id
     * @ORM\Column(name="supplier_category_id", type="integer", length=11)
     */
    private $supplierCategoryId;

    /**
     * 供应商编号
     * @ORM\Column(name="supplier_code", type="string", length=20)
     */
    private $supplierCode;

    /**
     * 供应商名称
     * @ORM\Column(name="supplier_name", type="string", length=100)
     */
    private $supplierName;

    /**
     * 供应商排序
     * @ORM\Column(name="supplier_sort", type="integer", length=11)
     */
    private $supplierSort;

    /**
     * 地区id
     * @ORM\Column(name="region_id", type="integer", length=11)
     */
    private $regionId;

    /**
     * 地区信息
     * @ORM\Column(name="region_values", type="string", length=100)
     */
    private $regionValues;

    /**
     * 供应商地址
     * @ORM\Column(name="supplier_address", type="string", length=255)
     */
    private $supplierAddress;

    /**
     * 联系人
     * @ORM\Column(name="supplier_contacts", type="string", length=30)
     */
    private $supplierContacts;

    /**
     * 手机号码
     * @ORM\Column(name="supplier_phone", type="string", length=20)
     */
    private $supplierPhone;

    /**
     * 电话号码
     * @ORM\Column(name="supplier_telephone", type="string", length=20)
     */
    private $supplierTelephone;

    /**
     * 开户行
     * @ORM\Column(name="supplier_bank", type="string", length=100)
     */
    private $supplierBank;

    /**
     * 银行账号
     * @ORM\Column(name="supplier_bank_account", type="string", length=30)
     */
    private $supplierBankAccount;

    /**
     * 税号
     * @ORM\Column(name="supplier_tax", type="string", length=30)
     */
    private $supplierTax;

    /**
     * 电子邮箱
     * @ORM\Column(name="supplier_email", type="string", length=30)
     */
    private $supplierEmail;

    /**
     * 备注信息
     * @ORM\Column(name="supplier_info", type="string", length=255)
     */
    private $supplierInfo;

    /**
     * 管理员id
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * 供应商分类
     * @ORM\ManyToOne(targetEntity="Customer\Entity\SupplierCategory", inversedBy="suppliers")
     * @ORM\JoinColumn(name="supplier_category_id", referencedColumnName="supplier_category_id")
     */
    private $supplierCategory;

    /**
     * 采购订单
     * @ORM\OneToMany(targetEntity="Purchase\Entity\Order", mappedBy="oneSupplier")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="supplier_id")
     */
    private $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getOrders()
    {
        return $this->orders;
    }
    public function addOrders($order)
    {
        $this->orders[] = $order;
    }

    /**
     * @return mixed
     */
    public function getSupplierCategory()
    {
        return $this->supplierCategory;
    }

    /**
     * @param mixed $supplierCategory
     */
    public function setSupplierCategory(SupplierCategory $supplierCategory)
    {
        $this->supplierCategory = $supplierCategory;
        $supplierCategory->addSuppliers($this);
    }

    /**
     * @return mixed
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * @param mixed $supplierId
     */
    public function setSupplierId($supplierId)
    {
        $this->supplierId = $supplierId;
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
    public function getSupplierCode()
    {
        return $this->supplierCode;
    }

    /**
     * @param mixed $supplierCode
     */
    public function setSupplierCode($supplierCode)
    {
        $this->supplierCode = $supplierCode;
    }

    /**
     * @return mixed
     */
    public function getSupplierName()
    {
        return $this->supplierName;
    }

    /**
     * @param mixed $supplierName
     */
    public function setSupplierName($supplierName)
    {
        $this->supplierName = $supplierName;
    }

    /**
     * @return mixed
     */
    public function getSupplierSort()
    {
        return $this->supplierSort;
    }

    /**
     * @param mixed $supplierSort
     */
    public function setSupplierSort($supplierSort)
    {
        $this->supplierSort = $supplierSort;
    }

    /**
     * @return mixed
     */
    public function getRegionId()
    {
        return $this->regionId;
    }

    /**
     * @param mixed $regionId
     */
    public function setRegionId($regionId)
    {
        $this->regionId = $regionId;
    }

    /**
     * @return mixed
     */
    public function getRegionValues()
    {
        return $this->regionValues;
    }

    /**
     * @param mixed $regionValues
     */
    public function setRegionValues($regionValues)
    {
        $this->regionValues = $regionValues;
    }

    /**
     * @return mixed
     */
    public function getSupplierAddress()
    {
        return $this->supplierAddress;
    }

    /**
     * @param mixed $supplierAddress
     */
    public function setSupplierAddress($supplierAddress)
    {
        $this->supplierAddress = $supplierAddress;
    }

    /**
     * @return mixed
     */
    public function getSupplierContacts()
    {
        return $this->supplierContacts;
    }

    /**
     * @param mixed $supplierContacts
     */
    public function setSupplierContacts($supplierContacts)
    {
        $this->supplierContacts = $supplierContacts;
    }

    /**
     * @return mixed
     */
    public function getSupplierPhone()
    {
        return $this->supplierPhone;
    }

    /**
     * @param mixed $supplierPhone
     */
    public function setSupplierPhone($supplierPhone)
    {
        $this->supplierPhone = $supplierPhone;
    }

    /**
     * @return mixed
     */
    public function getSupplierTelephone()
    {
        return $this->supplierTelephone;
    }

    /**
     * @param mixed $supplierTelephone
     */
    public function setSupplierTelephone($supplierTelephone)
    {
        $this->supplierTelephone = $supplierTelephone;
    }

    /**
     * @return mixed
     */
    public function getSupplierBank()
    {
        return $this->supplierBank;
    }

    /**
     * @param mixed $supplierBank
     */
    public function setSupplierBank($supplierBank)
    {
        $this->supplierBank = $supplierBank;
    }

    /**
     * @return mixed
     */
    public function getSupplierBankAccount()
    {
        return $this->supplierBankAccount;
    }

    /**
     * @param mixed $supplierBankAccount
     */
    public function setSupplierBankAccount($supplierBankAccount)
    {
        $this->supplierBankAccount = $supplierBankAccount;
    }

    /**
     * @return mixed
     */
    public function getSupplierTax()
    {
        return $this->supplierTax;
    }

    /**
     * @param mixed $supplierTax
     */
    public function setSupplierTax($supplierTax)
    {
        $this->supplierTax = $supplierTax;
    }

    /**
     * @return mixed
     */
    public function getSupplierEmail()
    {
        return $this->supplierEmail;
    }

    /**
     * @param mixed $supplierEmail
     */
    public function setSupplierEmail($supplierEmail)
    {
        $this->supplierEmail = $supplierEmail;
    }

    /**
     * @return mixed
     */
    public function getSupplierInfo()
    {
        return $this->supplierInfo;
    }

    /**
     * @param mixed $supplierInfo
     */
    public function setSupplierInfo($supplierInfo)
    {
        $this->supplierInfo = $supplierInfo;
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