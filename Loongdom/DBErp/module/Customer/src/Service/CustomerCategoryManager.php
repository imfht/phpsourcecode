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

namespace Customer\Service;

use Customer\Entity\Customer;
use Customer\Entity\CustomerCategory;
use Doctrine\ORM\EntityManager;

class CustomerCategoryManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加客户分类
     * @param array $data
     * @param int $adminId
     * @return CustomerCategory
     */
    public function addCustomerCategory(array $data, int $adminId)
    {
        $customerCategory = new CustomerCategory();
        $customerCategory->valuesSet($data);

        $customerCategory->setAdminId($adminId);

        $this->entityManager->persist($customerCategory);
        $this->entityManager->flush();

        return $customerCategory;
    }

    /**
     * 编辑客户分类
     * @param array $data
     * @param CustomerCategory $customerCategory
     * @return bool
     */
    public function editCustomerCategory(array $data, CustomerCategory $customerCategory)
    {
        $customerCategory->valuesSet($data);

        $this->entityManager->flush();

        return true;
    }

    /**
     * 批量处理
     * @param array $data
     */
    public function editAllCustomerCategory(array $data)
    {
        foreach ($data['select_id'] as $key => $value) {
            $category = $this->entityManager->getRepository(CustomerCategory::class)->findOneByCustomerCategoryId($value);

            if($data['editAllState'] == 'sort') {
                $category->setCustomerCategorySort($data['customer_category_sort'][$value]);
            }

            $this->entityManager->flush();
            $this->entityManager->clear(CustomerCategory::class);
        }
    }

    /**
     * 删除处理
     * @param CustomerCategory $customerCategory
     * @return bool
     */
    public function deleteCustomerCategory(CustomerCategory $customerCategory)
    {
        $customers = $this->entityManager->getRepository(Customer::class)->findBy(['customerCategoryId' => $customerCategory->getCustomerCategoryId()]);
        if($customers) return false;

        $this->entityManager->remove($customerCategory);
        $this->entityManager->flush();

        return true;
    }
}