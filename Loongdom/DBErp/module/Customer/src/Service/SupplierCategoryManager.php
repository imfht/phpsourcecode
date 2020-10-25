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

class SupplierCategoryManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加供应商分类
     * @param array $data
     * @param int $adminId
     * @return SupplierCategory
     */
    public function addSupplierCategory(array $data, int $adminId)
    {
        $supplierCategory = new SupplierCategory();
        $supplierCategory->valuesSet($data);
        $supplierCategory->setAdminId($adminId);

        $this->entityManager->persist($supplierCategory);
        $this->entityManager->flush();

        return $supplierCategory;
    }

    /**
     * 编辑供应商分类
     * @param array $data
     * @param SupplierCategory $supplierCategory
     * @return bool
     */
    public function editSupplierCategory(array $data, SupplierCategory $supplierCategory)
    {
        $supplierCategory->valuesSet($data);

        $this->entityManager->flush();

        return true;
    }

    /**
     * 批量编辑
     * @param array $data
     */
    public function editAllSupplierCategory(array $data)
    {
        foreach ($data['select_id'] as $key => $value) {
            $category = $this->entityManager->getRepository(SupplierCategory::class)->findOneBySupplierCategoryId($value);

            if($data['editAllState'] == 'sort') {
                $category->setSupplierCategorySort($data['supplier_category_sort'][$value]);
            }

            $this->entityManager->flush();
            $this->entityManager->clear(SupplierCategory::class);
        }
    }

    /**
     * 删除供应商分类
     * @param SupplierCategory $supplierCategory
     * @return bool
     */
    public function deleteSupplierCategory(SupplierCategory $supplierCategory)
    {
        $suppliers = $this->entityManager->getRepository(Supplier::class)->findBy(['supplierCategoryId' => $supplierCategory->getSupplierCategoryId()]);
        if($suppliers) return false;

        $this->entityManager->remove($supplierCategory);
        $this->entityManager->flush();

        return true;
    }
}