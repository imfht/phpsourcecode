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
 * The repository for Filetypes
 */
class FiletypesRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * 查询文件类型
     * @param string $types
     * @return unknown
     */
    public function findTypes($types=""){
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(FALSE);
        $arr=array();
        $arr[] = $query->in('uid',explode(",",$types));
        if(!empty($arr)){
            $query->matching($query->logicalAnd($arr));
        }
        $query->setOrderings(array(
            'uid' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
        ));
        return $query->execute();
    }
}
