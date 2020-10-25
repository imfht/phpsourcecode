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

class CustomerManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加客户
     * @param array $data
     * @param int $adminId
     * @return Customer
     */
    public function addCustomer(array $data, int $adminId)
    {
        $category = $this->entityManager->getRepository(CustomerCategory::class)->findOneByCustomerCategoryId($data['customerCategoryId']);

        $customer = new Customer();
        $customer->valuesSet($data);
        $customer->setAdminId($adminId);

        $customer->setCustomerCategory($category);

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return $customer;
    }

    /**
     * 编辑客户
     * @param array $data
     * @param Customer $customer
     * @return bool
     */
    public function editCustomer(array $data, Customer $customer)
    {
        $category = $this->entityManager->getRepository(CustomerCategory::class)->findOneByCustomerCategoryId($data['customerCategoryId']);

        $customer->valuesSet($data);

        $customer->setCustomerCategory($category);

        $this->entityManager->flush();

        return true;
    }

    /**
     * 批量修改
     * @param array $data
     */
    public function editAllCustomer(array $data)
    {
        foreach ($data['select_id'] as $key => $value) {
            $customer = $this->entityManager->getRepository(Customer::class)->findOneByCustomerId($value);

            if($data['editAllState'] == 'sort') {
                $customer->setCustomerSort($data['customer_sort'][$value]);
            }

            $this->entityManager->flush();
            $this->entityManager->clear(Customer::class);
        }
    }

    /**
     * 删除客户
     * @param Customer $customer
     * @return bool
     */
    public function deleteCustomer(Customer $customer)
    {
        $this->entityManager->remove($customer);
        $this->entityManager->flush();

        return true;
    }
}