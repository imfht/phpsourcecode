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
use Doctrine\ORM\Mapping as ORM;

/**
 * 客户
 * @package Customer\Entity
 * @ORM\Entity(repositoryClass="Customer\Repository\CustomerRepository")
 * @ORM\Table(name="dberp_customer")
 */
class Customer extends BaseEntity
{
    /**
     * 客户id
     * @ORM\Id()
     * @ORM\Column(name="customer_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $customerId;

    /**
     * 客户分类id
     * @ORM\Column(name="customer_category_id", type="integer", length=11)
     */
    private $customerCategoryId;

    /**
     * 客户编号
     * @ORM\Column(name="customer_code", type="string", length=20)
     */
    private $customerCode;

    /**
     * 客户名称
     * @ORM\Column(name="customer_name", type="string", length=100)
     */
    private $customerName;

    /**
     * 客户排序
     * @ORM\Column(name="customer_sort", type="integer", length=11)
     */
    private $customerSort;

    /**
     * 客户电子邮箱
     * @ORM\Column(name="customer_email", type="string", length=30)
     */
    private $customerEmail;

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
     * 客户地址
     * @ORM\Column(name="customer_address", type="string", length=255)
     */
    private $customerAddress;

    /**
     * 客户联系人
     * @ORM\Column(name="customer_contacts", type="string", length=30)
     */
    private $customerContacts;

    /**
     * 客户手机
     * @ORM\Column(name="customer_phone", type="string", length=20)
     */
    private $customerPhone;

    /**
     * 客户座机
     * @ORM\Column(name="customer_telephone", type="string", length=20)
     */
    private $customerTelephone;

    /**
     * 开户行
     * @ORM\Column(name="customer_bank", type="string", length=100)
     */
    private $customerBank;

    /**
     * 银行账号
     * @ORM\Column(name="customer_bank_account", type="string", length=30)
     */
    private $customerBankAccount;

    /**
     * 税号
     * @ORM\Column(name="customer_tax", type="string", length=30)
     */
    private $customerTax;

    /**
     * 备注
     * @ORM\Column(name="customer_info", type="string", length=255)
     */
    private $customerInfo;

    /**
     * 管理员id
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * 与客户分类进行连接
     * @ORM\ManyToOne(targetEntity="Customer\Entity\CustomerCategory", inversedBy="customers")
     * @ORM\JoinColumn(name="customer_category_id", referencedColumnName="customer_category_id")
     */
    private $customerCategory;

    /**
     * @return mixed
     */
    public function getCustomerCategory()
    {
        return $this->customerCategory;
    }

    /**
     * @param mixed $customerCategory
     */
    public function setCustomerCategory(CustomerCategory $customerCategory)
    {
        $this->customerCategory = $customerCategory;
        $customerCategory->addCustomers($this);
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param mixed $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @return mixed
     */
    public function getCustomerCategoryId()
    {
        return $this->customerCategoryId;
    }

    /**
     * @param mixed $customerCategoryId
     */
    public function setCustomerCategoryId($customerCategoryId)
    {
        $this->customerCategoryId = $customerCategoryId;
    }

    /**
     * @return mixed
     */
    public function getCustomerCode()
    {
        return $this->customerCode;
    }

    /**
     * @param mixed $customerCode
     */
    public function setCustomerCode($customerCode)
    {
        $this->customerCode = $customerCode;
    }

    /**
     * @return mixed
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }

    /**
     * @param mixed $customerName
     */
    public function setCustomerName($customerName)
    {
        $this->customerName = $customerName;
    }

    /**
     * @return mixed
     */
    public function getCustomerSort()
    {
        return $this->customerSort;
    }

    /**
     * @param mixed $customerSort
     */
    public function setCustomerSort($customerSort)
    {
        $this->customerSort = $customerSort;
    }

    /**
     * @return mixed
     */
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }

    /**
     * @param mixed $customerEmail
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->customerEmail = $customerEmail;
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
    public function getCustomerAddress()
    {
        return $this->customerAddress;
    }

    /**
     * @param mixed $customerAddress
     */
    public function setCustomerAddress($customerAddress)
    {
        $this->customerAddress = $customerAddress;
    }

    /**
     * @return mixed
     */
    public function getCustomerContacts()
    {
        return $this->customerContacts;
    }

    /**
     * @param mixed $customerContacts
     */
    public function setCustomerContacts($customerContacts)
    {
        $this->customerContacts = $customerContacts;
    }

    /**
     * @return mixed
     */
    public function getCustomerPhone()
    {
        return $this->customerPhone;
    }

    /**
     * @param mixed $customerPhone
     */
    public function setCustomerPhone($customerPhone)
    {
        $this->customerPhone = $customerPhone;
    }

    /**
     * @return mixed
     */
    public function getCustomerTelephone()
    {
        return $this->customerTelephone;
    }

    /**
     * @param mixed $customerTelephone
     */
    public function setCustomerTelephone($customerTelephone)
    {
        $this->customerTelephone = $customerTelephone;
    }

    /**
     * @return mixed
     */
    public function getCustomerBank()
    {
        return $this->customerBank;
    }

    /**
     * @param mixed $customerBank
     */
    public function setCustomerBank($customerBank)
    {
        $this->customerBank = $customerBank;
    }

    /**
     * @return mixed
     */
    public function getCustomerBankAccount()
    {
        return $this->customerBankAccount;
    }

    /**
     * @param mixed $customerBankAccount
     */
    public function setCustomerBankAccount($customerBankAccount)
    {
        $this->customerBankAccount = $customerBankAccount;
    }

    /**
     * @return mixed
     */
    public function getCustomerTax()
    {
        return $this->customerTax;
    }

    /**
     * @param mixed $customerTax
     */
    public function setCustomerTax($customerTax)
    {
        $this->customerTax = $customerTax;
    }

    /**
     * @return mixed
     */
    public function getCustomerInfo()
    {
        return $this->customerInfo;
    }

    /**
     * @param mixed $customerInfo
     */
    public function setCustomerInfo($customerInfo)
    {
        $this->customerInfo = $customerInfo;
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