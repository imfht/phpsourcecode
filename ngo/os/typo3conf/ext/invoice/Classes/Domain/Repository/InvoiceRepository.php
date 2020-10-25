<?php
namespace Jykj\Invoice\Domain\Repository;


/***
 *
 * This file is part of the "发票管理" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Shichang Yang <yangshichang@ngoos.org>, 极益科技
 *
 ***/
/**
 * The repository for Invoices
 */
class InvoiceRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * 查询发票申请列表
     * @param string $keyword
     * @return unknown
     */
    public function findAlls($keyword){
        $query = $this->createQuery();
        $condition=array();
        if($keyword != ''){
            $condition[] = $query->logicalOr(array(
                $query->like('money', '%' . $keyword . '%'),
                $query->like('header', '%' . $keyword . '%'),
                $query->like('address', '%' . $keyword . '%'),
                $query->like('postcode', '%' . $keyword . '%'),
                $query->like('people', '%' . $keyword . '%'),
                $query->like('telphone', '%' . $keyword . '%'),
                $query->like('channelid.name', '%' . $keyword . '%'),
                $query->like('mail', '%' . $keyword . '%'),
                $query->like('spare1', '%' . $keyword . '%')
            ));
        }
        
        if(!empty($condition)){
            $query->matching($query->logicalAnd($condition));
        }
        
        $query->setOrderings(array(
            'donatetime' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
        ));
        return $query->execute();
    }
}
