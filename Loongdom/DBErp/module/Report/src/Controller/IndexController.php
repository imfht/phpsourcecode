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

namespace Report\Controller;

use Doctrine\ORM\EntityManager;
use Report\Report\SalesReport;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

class IndexController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $salesReport;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager,
        SalesReport     $salesReport
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->salesReport      = $salesReport;
    }

    public function indexAction()
    {
        $array = [];

        $array['salesOrderCount']   = $this->salesReport->salesOrderCount() + $this->salesReport->salesOrderCount();
        $array['salesAmount']       = $this->salesReport->salesAmount() + $this->salesReport->shopAmount();
        $array['customerCount']     = $this->salesReport->salesCustomerCount() + $this->salesReport->shopBuyUserCount();
        $array['goodsCount']        = $this->salesReport->salesGoodsCount() + $this->salesReport->shopGoodsCount();

        return $array;
    }
}