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

namespace Store\Service;

use Doctrine\ORM\EntityManager;
use Store\Entity\Brand;

class BrandManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * 添加品牌
     * @param array $data
     * @param int $adminId
     * @return Brand
     */
    public function addBrand(array $data, int $adminId)
    {
        $brand =new Brand();
        $brand->valuesSet($data);
        $brand->setAdminId($adminId);

        $this->entityManager->persist($brand);
        $this->entityManager->flush();

        return $brand;
    }

    /**
     * 编辑更新品牌
     * @param array $data
     * @param Brand $brand
     * @return bool
     */
    public function editBrand(array $data, Brand $brand)
    {
        $brand->valuesSet($data);

        $this->entityManager->flush();

        return true;
    }

    /**
     * 批量处理
     * @param array $data
     */
    public function updateAllBrand(array $data)
    {
        foreach ($data['select_id'] as $key => $value) {
            $brand = $this->entityManager->getRepository(Brand::class)->findOneByBrandId($value);

            if($data['editAllState'] == 'sort') {
                $brand->setBrandSort($data['brand_sort'][$value]);
            }

            $this->entityManager->flush();
            $this->entityManager->clear(Brand::class);
        }
    }

    /**
     * 删除品牌
     * @param Brand $brand
     * @return bool
     */
    public function deleteBrand(Brand $brand)
    {
        $this->entityManager->remove($brand);
        $this->entityManager->flush();

        return true;
    }
}