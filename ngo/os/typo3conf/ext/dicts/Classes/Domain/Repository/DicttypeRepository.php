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
 * The repository for Dicttypes
 */
class DicttypeRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * 查询字典大类信息
     *
     * @param string $keyword
     */
    public function findAlls($keyword)
    {
        $query = $this->createQuery();
        $arr = [];
        if ($keyword != '') {
            $arr[] = $query->like('name', '%' . $keyword . '%');
        }
        if (!empty($arr)) {
            $query->matching($query->logicalAnd($arr));
        }
        $query->setOrderings([
            'sort' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
            'crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
        ]);
        return $query->execute();
    } 
}
