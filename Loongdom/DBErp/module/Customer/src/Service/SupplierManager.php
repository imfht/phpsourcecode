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

use Customer\Entity\Supplier;
use Customer\Entity\SupplierCategory;
use Doctrine\ORM\EntityManager;

class SupplierManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加供应商
     * @param array $data
     * @param int $adminId
     * @return Supplier
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addSupplier(array $data, int $adminId)
    {
        $supplierCategory = $this->entityManager->getRepository(SupplierCategory::class)->findOneBySupplierCategoryId($data['supplierCategoryId']);

        $supplier = new Supplier();
        $supplier->valuesSet($data);
        $supplier->setAdminId($adminId);

        $supplier->setSupplierCategory($supplierCategory);

        $this->entityManager->persist($supplier);
        $this->entityManager->flush();

        return $supplier;
    }

    /**
     * 编辑供应商信息
     * @param array $data
     * @param Supplier $supplier
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editSupplier(array $data, Supplier $supplier)
    {
        $supplierCategory = $this->entityManager->getRepository(SupplierCategory::class)->findOneBySupplierCategoryId($data['supplierCategoryId']);

        $supplier->valuesSet($data);

        $supplier->setSupplierCategory($supplierCategory);

        $this->entityManager->flush();

        return true;
    }

    /**
     * 批量更新
     * @param array $data
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editAllSupplier(array $data)
    {
        foreach ($data['select_id'] as $key => $value) {
            $supplier = $this->entityManager->getRepository(Supplier::class)->findOneBySupplierId($value);

            if($data['editAllState'] == 'sort') {
                $supplier->setSupplierSort($data['supplier_sort'][$value]);
            }

            $this->entityManager->flush();
            $this->entityManager->clear(Supplier::class);
        }
    }

    /**
     * 删除供应商
     * @param Supplier $supplier
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteSupplier(Supplier $supplier)
    {
        $this->entityManager->remove($supplier);
        $this->entityManager->flush();

        return true;
    }
}