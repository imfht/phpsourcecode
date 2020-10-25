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
 * The repository for Areas
 */
class AreaRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
	/**
     * 查询字典项目信息
     *
     * @param string $parentid
     * @param $dicttype
     */
    public function findAllArea($parentid)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(FALSE); //忽略pid
        $arr = [];
        $arr[] = $query->equals('parentuid', $parentid);
        
        if (!empty($arr)) {
            $query->matching($query->logicalAnd($arr));
        }
        $query->setOrderings([
            'uid' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
        ]);
        return $query->execute();
    }
}
