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

namespace Admin\Controller;

use Admin\Report\HomeReport;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

class HomeController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $homeReport;
    private $i18nSessionContainer;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager,
        HomeReport      $homeReport,
        $i18nSessionContainer
    )
    {
        $this->translator           = $translator;
        $this->entityManager        = $entityManager;
        $this->homeReport           = $homeReport;
        $this->i18nSessionContainer = $i18nSessionContainer;
    }

    /**
     * 后台首页
     * @return array
     */
    public function indexAction()
    {
        $array = [];

        $array['goodsCount']        = $this->homeReport->goodsCount();
        $array['purchaseAmount']    = $this->homeReport->purchaseAmount();
        $array['salesAmount']       = $this->homeReport->salesAmount();
        $array['customerCount']     = $this->homeReport->customerCount() + $this->homeReport->supplierCount();

        $array['purchaseOrder']     = $this->homeReport->purchaseOrderLimit();
        $array['salesOrder']        = $this->homeReport->salesOrderLimit();

        return $array;
    }

    public function notAuthorizedAction()
    {
        return [];
    }
}