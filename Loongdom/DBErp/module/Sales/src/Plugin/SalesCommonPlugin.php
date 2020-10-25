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

namespace Sales\Plugin;

use Doctrine\ORM\EntityManager;
use Sales\Service\SalesOperLogManager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\I18n\Translator;
use Zend\Session\Container;

class SalesCommonPlugin extends AbstractPlugin
{
    private $entityManager;
    private $translator;
    private $salesOperLogManager;

    private $adminSession;

    public function __construct(
        EntityManager   $entityManager,
        Translator      $translator,
        SalesOperLogManager $salesOperLogManager
    )
    {
        $this->entityManager    = $entityManager;
        $this->translator       = $translator;
        $this->salesOperLogManager = $salesOperLogManager;

        $this->adminSession     = new Container('admin');
    }

    /**
     * 添加销售订单操作记录
     * @param $orderState
     * @param $orderId
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addSalesOperLog($orderState, $orderId, $time = '')
    {
        $this->salesOperLogManager->addSalesOperLog(
            [
                'salesOrderId'  => $orderId,
                'orderState'    => $orderState,
                'operUserId'    => $this->adminSession->admin_id,
                'operUser'      => $this->adminSession->admin_name,
                'operTime'      => empty($time) ? time() : $time
            ]
        );
    }

    /**
     * 删除销售订单操作记录
     * @param $orderId
     * @param bool $return
     */
    public function delSalesOperLog($orderId, $return = false)
    {
        if($return) $this->salesOperLogManager->delSalesReturnOperLog($orderId);
        else $this->salesOperLogManager->delSalesOperLog($orderId);
    }

    /**
     * 销售单号
     * @param string $prefix
     * @return string
     */
    public function createSalesOrderSn($prefix = 'S')
    {
        $adminId = $this->adminSession->admin_id;

        return $prefix . (strlen($adminId) > 3 ? substr($adminId, -3) : str_pad($adminId, 3, '0', STR_PAD_LEFT)) . date("YmdHis", time());
    }

    /**
     * 销售发货单号生成
     * @param string $prefix
     * @return string
     */
    public function createSendOrderSn($prefix = 'H')
    {
        return $this->createSalesOrderSn($prefix);
    }
}