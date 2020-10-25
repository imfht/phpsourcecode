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
 * 客户分类
 * @package Customer\Entity
 * @ORM\Entity(repositoryClass="Customer\Repository\CustomerCategoryRepository")
 * @ORM\Table(name="dberp_customer_category")
 */
class CustomerCategory extends BaseEntity
{
    /**
     * 客户分类
     * @ORM\Id()
     * @ORM\Column(name="customer_category_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $customerCategoryId;

    /**
     * 客户分类编号
     * @ORM\Column(name="customer_category_code", type="string", length=20)
     */
    private $customerCategoryCode;

    /**
     * 客户分类名称
     * @ORM\Column(name="customer_category_name", type="string", length=100)
     */
    private $customerCategoryName;

    /**
     * 客户分类排序
     * @ORM\Column(name="customer_category_sort", type="integer", length=11)
     */
    private $customerCategorySort;

    /**
     * 管理员id
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * 与客户进行连接
     * @ORM\OneToMany(targetEntity="Customer\Entity\Customer", mappedBy="customerCategory")
     * @ORM\JoinColumn(name="customer_category_id", referencedColumnName="customer_category_id")
     */
    private $customers;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getCustomers()
    {
        return $this->customers;
    }

    public function addCustomers($customer)
    {
        $this->customers[] = $customer;
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
    public function getCustomerCategoryCode()
    {
        return $this->customerCategoryCode;
    }

    /**
     * @param mixed $customerCategoryCode
     */
    public function setCustomerCategoryCode($customerCategoryCode)
    {
        $this->customerCategoryCode = $customerCategoryCode;
    }

    /**
     * @return mixed
     */
    public function getCustomerCategoryName()
    {
        return $this->customerCategoryName;
    }

    /**
     * @param mixed $customerCategoryName
     */
    public function setCustomerCategoryName($customerCategoryName)
    {
        $this->customerCategoryName = $customerCategoryName;
    }

    /**
     * @return mixed
     */
    public function getCustomerCategorySort()
    {
        return $this->customerCategorySort;
    }

    /**
     * @param mixed $customerCategorySort
     */
    public function setCustomerCategorySort($customerCategorySort)
    {
        $this->customerCategorySort = $customerCategorySort;
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