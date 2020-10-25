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

namespace Admin\Service;

use Admin\Entity\Region;
use Doctrine\ORM\EntityManager;

class RegionManager
{
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;

    }

    /**
     * 添加地区
     * @param array $data
     */
    public function addRegion(array $data)
    {
        $classPath = '';
        if($data['regionTopId'] > 0) {
            $regionInfo = $this->entityManager->getRepository(Region::class)->findOneByRegionId($data['regionTopId']);
            if($regionInfo) $classPath = $regionInfo->getRegionPath();
        }

        $regionArray = explode("\r\n", $data['regionName']);
        foreach ($regionArray as $value) {
            $data['regionName'] = $value;
            $region = new Region();
            $region->valuesSet($data);

            $this->entityManager->persist($region);
            $this->entityManager->flush();

            $oneClassPath = '';
            $oneClassPath = empty($classPath) ? $region->getRegionId() : $classPath . ',' . $region->getRegionId();
            $region->setRegionPath($oneClassPath);
            $this->entityManager->flush();

            $this->entityManager->clear(Region::class);
        }
    }

    /**
     * 更新地区
     * @param array $data
     * @param Region $region
     * @return bool
     */
    public function editRegion(array $data, Region $region)
    {
        $region->setRegionName($data['regionName']);
        $region->setRegionSort($data['regionSort']);

        $this->entityManager->flush();

        return true;
    }

    /**
     * 批量处理
     * @param array $data
     */
    public function updateAllRegion(array $data)
    {
        foreach ($data['select_id'] as $key => $value) {
            $region = $this->entityManager->getRepository(Region::class)->findOneByRegionId($value);

            if($data['editAllState'] == 'sort') {
                $region->setRegionSort($data['region_sort'][$value]);
            }
            if($data['editAllState'] == 'del') {
                $subRegionArray = $this->entityManager->getRepository(Region::class)->findBy(['regionTopId' => $region->getRegionId()]);
                if($subRegionArray) {
                    $this->entityManager->clear(Region::class);
                    continue;
                }

                $this->entityManager->remove($region);
            }

            $this->entityManager->flush();
            $this->entityManager->clear(Region::class);
        }
    }

    /**
     * 删除地区
     * @param Region $region
     * @return bool
     */
    public function deleteRegion(Region $region)
    {
        $this->entityManager->remove($region);
        $this->entityManager->flush();

        return true;
    }
}