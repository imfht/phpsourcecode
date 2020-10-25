<?php
namespace Jykj\CaseTab\Domain\Repository;


/***
 *
 * This file is part of the "应用案例" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 杨世昌 <yangshichang@ngoos.org>, 极益科技
 *
 ***/
/**
 * The repository for Casetabs
 */
class CasetabRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * 查询所有符合条件的案例
     * @param string $keyword
     * @param string $industry
     * @param string $product
     * @param string $labels
     * @return unknown
     */
    public function findAlls($keyword,$industry,$product,$labels,$pagesize,$nowpage){
        $query = $this->createQuery();
        $arr=array();
        if($keyword != ''){
            $arr[] = $query->like('title', '%' . $keyword . '%');
        }
        
        if($industry!=""){
            $arr[]=$query->equals("industry",$industry);
        }
        
        if($product!=""){
            $arr[]=$query->like("product","%,".$product.",%");
        }
        
        if($labels!=""){
            $arr[]=$query->like("labels","%,".$labels.",%");
        }
        
        if(!empty($arr)){
            $query->matching($query->logicalAnd($arr));
        }
        
        $query->setOrderings(array(
            'crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
        ));
        
        if($nowpage>0&&$pagesize>0){
            $query->setOffset(((int)$nowpage-1)*(int)$pagesize); //从第几条记录开始
            $query->setLimit((int)$pagesize); //每页记录数
        }
        
        return $query->execute();
    }
    
    /**
     * 查询指定行业的数据
     * @param int $industry
     * @param int $limit
     * @return unknown
     */
    public function findCaseList($industry,$limit){
        $query = $this->createQuery();
        $arr=array();
        
        $arr[]=$query->equals("industry",$industry);
        
        if(!empty($arr)){
            $query->matching($query->logicalAnd($arr));
        }
        
        $query->setOrderings(array(
            'datetime' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
        ));
        $query->setLimit((int)$limit);
        return $query->execute();
    }
    
}
