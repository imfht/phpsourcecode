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

namespace Purchase\View\Helper;

use Admin\Data\Common;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\I18n\Translator;
use Zend\View\Helper\AbstractHelper;

class PurchaseHelper extends AbstractHelper
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
     * 采购订单支付方式
     * @param $paymentCode
     * @return mixed
     */
    public function orderPayment($paymentCode)
    {
        $payment = Common::payment($this->translator);

        return $payment[$paymentCode];
    }

    /**
     * 采购订单状态
     * @param $state
     * @param int $style
     * @return mixed
     */
    public function orderState($state, $style = 1)
    {
        $orderState = Common::purchaseOrderState($this->translator, $style);

        return $orderState[$state];
    }

    /**
     * 退货订单状态
     * @param $state
     * @param int $style
     * @return mixed
     */
    public function orderReturnState($state, $style = 1)
    {
        $returnState = Common::purchaseOrderReturnState($this->translator, $style);

        return $returnState[$state];
    }
}