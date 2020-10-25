<?php
namespace Jykj\Dicts\Domain\Repository;


/***
 *
 * This file is part of the "数据字典" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Shichang Yang <yangshichang@ngoos.org>, 极益科技
 *
 ***/
/**
 * The repository for Dictitems
 */
class DictitemRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * 查询字典项目信息
     *
     * @param string $keyword
     * @param $dicttype
     */
    public function findAlls($keyword, $dicttype)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(FALSE); //忽略pid
        $arr = [];
        $arr[] = $query->equals('dicttype', $dicttype);
        $arr[] = $query->equals('parentuid', 0);
        
        if ($keyword != '') {
            $arr[] = $query->like('name', '%' . $keyword . '%');
        }
        if (!empty($arr)) {
            $query->matching($query->logicalAnd($arr));
        }
        $query->setOrderings([
            'sort' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
        ]);
        return $query->execute();
    }
    
    /**
     * 查找是否存在
     *
     * @param string $dictitem
     * @param $dicttype
     */
    public function findIsExist($dicttype,$dictitem)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(FALSE); //忽略pid
        $arr = array();
        $arr[] = $query->equals('dicttype', $dicttype);
        
        $arr[] = $query->logicalOr(array(
            $query->equals('name', $dictitem),
            $query->equals('uid', $dictitem)
        ));
        
        if (!empty($arr)) {
            $query->matching($query->logicalAnd($arr));
        }
        $query->setOrderings([
            'sort' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
        ]);
        return $query->execute();
    }
}
