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

namespace Sales\View;

use Admin\Data\Common;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\I18n\Translator;
use Zend\View\Helper\AbstractHelper;

class SalesHelper extends AbstractHelper
{
    private $entityManager;
    private $translator;

    public function __construct(
        EntityManager   $entityManager,
        Translator      $translator
    )
    {
        $this->entityManager    = $entityManager;
        $this->translator       = $translator;
    }

    /**
     * 销售订单收款方式
     * @param $paymentCode
     * @return mixed
     */
    public function orderReceivables($receivablesCode)
    {
        $receivables = Common::receivable($this->translator);

        return $receivables[$receivablesCode];
    }

    /**
     * 销售订单状态
     * @param $state
     * @param int $style
     * @return mixed
     */
    public function salesOrderState($state, $style = 1)
    {
        $salesOrderState = Common::salesOrderState($this->translator, $style);

        return $salesOrderState[$state];
    }

    /**
     * 退货订单状态
     * @param $state
     * @param int $style
     * @return mixed
     */
    public function salesOrderReturnState($state, $style = 1)
    {
        $returnState = Common::salesOrderReturnState($this->translator, $style);

        return $returnState[$state];
    }
}