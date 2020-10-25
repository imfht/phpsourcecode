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

namespace Store\Plugin;

use Doctrine\ORM\EntityManager;
use Store\Entity\Brand;
use Store\Entity\GoodsCategory;
use Store\Entity\Unit;
use Store\Entity\Warehouse;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\I18n\Translator;

class StoreCommonPlugin extends AbstractPlugin
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
     * 获取单位列表
     * @return array
     */
    public function unitOptions()
    {
        $unitArray  = [0 => $this->translator->translate('选择单位')];
        $unitList   = $this->entityManager->getRepository(Unit::class)->findBy([], ['unitSort' => 'ASC']);
        if($unitList) {
            foreach ($unitList as $value) {
                $unitArray[$value->getUnitId()] = $value->getUnitName();
            }
        }
        return $unitArray;
    }

    /**
     * 获取品牌列表
     * @param string $topName
     * @return array
     */
    public function brandListOptions($topName = '')
    {
        $brandList  = [0 => empty($topName) ? $this->translator->translate('选择品牌') : $topName];
        $brand      = $this->entityManager->getRepository(Brand::class)->findBy([], ['brandSort' => 'ASC']);
        if($brand) {
            foreach ($brand as $value) {
                $brandList[$value->getBrandId()] = $value->getBrandName();
            }
        }
        return $brandList;
    }

    /**
     * 获取供应商列表
     * @param string $topName
     * @return array
     */
    public function warehouseListOptions($topName = '')
    {
        $warehouseList = ['0' => empty($topName) ? $this->translator->translate('选择仓库') : $topName];
        $warehouseArray= $this->entityManager->getRepository(Warehouse::class)->findBy([], ['warehouseSort' => 'ASC']);
        if($warehouseArray) {
            foreach ($warehouseArray as $value) {
                $warehouseList[$value->getWarehouseId()] = $value->getWarehouseName() . '[' . $value->getWarehouseSn() . ']';
            }
        }
        return $warehouseList;
    }

    /**
     * 获取分类列表
     * @param string $topName
     * @return array
     */
    public function categoryListOptions($topName = '')
    {
        $goodsCategoryList = ['0' => empty($topName) ? $this->translator->translate('===顶级分类===') : $topName];
        $goodsCategory      = $this->entityManager->getRepository(GoodsCategory::class)->findBy([], ['goodsCategoryTopId' => 'ASC', 'goodsCategorySort' => 'ASC']);
        if($goodsCategory) {
            $nbspStr = html_entity_decode('&nbsp;', ENT_COMPAT, 'UTF-8');
            $categoryArray = $this->categoryOptions($goodsCategory);
            foreach ($categoryArray as $value) {
                if($value->getGoodsCategoryTopId() > 0) {
                    $goodsCategoryList[$value->getGoodsCategoryId()] =  str_repeat($nbspStr, substr_count($value->getGoodsCategoryPath(), ',') * 3) . '|----' . $value->getGoodsCategoryName();
                } else {
                    $goodsCategoryList[$value->getGoodsCategoryId()] = $value->getGoodsCategoryName();
                }
            }
        }
        return $goodsCategoryList;
    }

    /**
     * 输出无限分类
     * @param $category
     * @param int $topId
     * @param string $topIdName
     * @param string $idName
     * @return array
     */
    public function categoryOptions($category, $topId=0 , $topIdName='getGoodsCategoryTopId', $idName='getGoodsCategoryId')
    {
        static $array = [];
        foreach ($category as $value) {
            if($value->$topIdName() == $topId) {
                $array[] = $value;
                $this->categoryOptions($category, $value->$idName());
            }
        }
        return $array;
    }
}