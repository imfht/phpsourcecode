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

namespace Customer\Plugin;

use Customer\Entity\Customer;
use Customer\Entity\CustomerCategory;
use Customer\Entity\Supplier;
use Customer\Entity\SupplierCategory;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\I18n\Translator;

class CustomerCommonPlugin extends AbstractPlugin
{
    private $entityManager;
    private $translator;

    public function __construct(
        EntityManager   $entityManager,
        Translator      $translator
    )
    {
        $this->entityManager    = $entityManager;
        $this->translator       = $translator;
    }

    /**
     * 获取供应商分类列表
     * @param string $topName
     * @return array
     */
    public function supplierCategoryOptions($topName = '')
    {
        $categoryList  = [0 => empty($topName) ? $this->translator->translate('选择供应商分类') : $topName];
        $category      = $this->entityManager->getRepository(SupplierCategory::class)->findBy([], ['supplierCategorySort' => 'ASC']);
        if($category) {
            foreach ($category as $value) {
                $categoryList[$value->getSupplierCategoryId()] = $value->getSupplierCategoryName();
            }
        }
        return $categoryList;
    }

    /**
     * 获取供应商列表
     * @param string $topName
     * @return array
     */
    public function supplierListOptions($topName = '')
    {
        $supplierList  = [0 => empty($topName) ? $this->translator->translate('选择供应商') : $topName];
        $supplier      = $this->entityManager->getRepository(Supplier::class)->findBy([], ['supplierSort' => 'ASC']);
        if($supplier) {
            foreach ($supplier as $value) {
                $supplierList[$value->getSupplierId()] = $value->getSupplierName();
            }
        }
        return $supplierList;
    }

    /**
     * 获取客户分类列表
     * @param string $topName
     * @return array
     */
    public function customerCategoryOptions($topName = '')
    {
        $categoryList  = [0 => empty($topName) ? $this->translator->translate('选择客户分类') : $topName];
        $category      = $this->entityManager->getRepository(CustomerCategory::class)->findBy([], ['customerCategorySort' => 'ASC']);
        if($category) {
            foreach ($category as $value) {
                $categoryList[$value->getCustomerCategoryId()] = $value->getCustomerCategoryName();
            }
        }
        return $categoryList;
    }

    /**
     * 获取客户列表
     * @param string $topName
     * @return array
     */
    public function customerListOption($topName = '')
    {
        $customerList   = [0 => empty($topName) ? $this->translator->translate('选择客户') : $topName];
        $customer       = $this->entityManager->getRepository(Customer::class)->findBy([], ['customerSort' => 'ASC']);
        if($customer) {
            foreach ($customer as $value) {
                $customerList[$value->getCustomerId()] = $value->getCustomerName();
            }
        }
        return $customerList;
    }
}