<?php
namespace Jykj\Filemanage\Domain\Repository;


/***
 *
 * This file is part of the "文件管理系统" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 yangshichang <yangshichang@ngoos.org>, 极益科技
 *
 ***/
/**
 * The repository for Filemanages
 */
class FilemanageRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * 查询
     * @param int $filetypeid
     * @param string $keyword
     */
    public function findFileList($filetypeid,$keyword,$types=0){
        $query = $this->createQuery();
        $arr=array();
        if($filetypeid!=""){
            $arr[] = $query->equals('filetypeid',$filetypeid);
        }
        if($keyword!=""){
            $arr[] = $query->like('title', "%".$keyword."%");
        }
        if($types!=0){
            $arr[] = $query->equals('filetypes',$types);
        }
        if(!empty($arr)){
            $query->matching($query->logicalAnd($arr));
        }
        $query->setOrderings(array(
            'sort' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
        ));
        return $query->execute();
    }

    /**
     * 首页查询
     * @param int $filetypeid
     */
    public function findFileSyList($filetypeid,$types=0,$num=6){
        $query = $this->createQuery();
        $arr=array();
        if($filetypeid!=""){
            $arr[] = $query->equals('filetypeid',$filetypeid);
        }
        if($types!=0){
            $arr[] = $query->equals('filetypes',$types);
        }
        if(!empty($arr)){
            $query->matching($query->logicalAnd($arr));
        }
        $query->setOrderings(array(
            'sort' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
        ));
        $query->setLimit($num); //每页记录数

        return $query->execute();
    }
}
