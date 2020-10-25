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
 * The repository for Casetypes
 */
class CasetypeRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * 查询所有类型
     * @param string $keyword
     */
    public function findAlls($keyword,$bnopid=0){
        $query = $this->createQuery();
        if($bnopid==1){
            $query->getQuerySettings()->setRespectStoragePage(FALSE);//忽略pid
        }
        $arr=array();
        if($keyword != ''){
            $arr[] = $query->like('name', '%' . $keyword . '%');
        }
        if(!empty($arr)){
            $query->matching($query->logicalAnd($arr));
        }
        
        $query->setOrderings(array(
            'sort' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
        ));
        return $query->execute();
    }
}
