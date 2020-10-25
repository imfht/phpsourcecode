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

namespace Purchase\Plugin;

use Doctrine\ORM\EntityManager;
use Purchase\Service\PurchaseOperLogManager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\I18n\Translator;
use Zend\Session\Container;

class PurchaseCommonPlugin extends AbstractPlugin
{
    private $entityManager;
    private $translator;
    private $purchaseOperLogManager;

    private $adminSession;

    public function __construct(
        EntityManager   $entityManager,
        Translator      $translator,
        PurchaseOperLogManager $purchaseOperLogManager
    )
    {
        $this->entityManager    = $entityManager;
        $this->translator       = $translator;
        $this->purchaseOperLogManager = $purchaseOperLogManager;

        $this->adminSession     = new Container('admin');
    }

    /**
     * 添加采购操作记录
     * @param $orderState
     * @param $orderId
     * @param string $time
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addPurchaseOperLog($orderState, $orderId, $time = '')
    {
        $this->purchaseOperLogManager->addPurchaseOperLog(
            [
                'pOrderId' => $orderId,
                'orderState' => $orderState,
                'operUserId'    => $this->adminSession->admin_id,
                'operUser'      => $this->adminSession->admin_name,
                'operTime'      => empty($time) ? time() : $time
            ]
        );
    }

    /**
     * 删除采购操作记录
     * @param $orderId
     * @param bool $return
     */
    public function delPurchaseOperLog($orderId, $return = false)
    {
        if($return) $this->purchaseOperLogManager->delPurchaseReturnOperLog($orderId);
        else $this->purchaseOperLogManager->delPurchaseOperLog($orderId);
    }

    /**
     * 订单编号，通用，该方法直接用与采购单
     * @param string $prefix
     * @return string
     */
    public function createOrderSn($prefix = 'P')
    {
        $adminId = $this->adminSession->admin_id;

        return $prefix . (strlen($adminId) > 3 ? substr($adminId, -3) : str_pad($adminId, 3, '0', STR_PAD_LEFT)) . date("YmdHis", time());
    }

    /**
     * 采购入库单号生成
     * @param string $prefix
     * @return string
     */
    public function createWarehouseOrderSn($prefix = 'W')
    {
        return $this->createOrderSn($prefix);
    }

}