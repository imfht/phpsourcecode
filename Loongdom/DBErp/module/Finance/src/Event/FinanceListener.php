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

namespace Finance\Event;

use Doctrine\ORM\EntityManager;
use Finance\Entity\Payable;
use Finance\Service\PayableManager;
use Finance\Service\ReceivableManager;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

class FinanceListener implements ListenerAggregateInterface
{
    protected $listeners = [];

    private $entityManager;
    private $payableManager;
    private $receivableManager;

    public function __construct(
        EntityManager   $entityManager,
        PayableManager  $payableManager,
        ReceivableManager $receivableManager
    )
    {
        $this->entityManager = $entityManager;
        $this->payableManager= $payableManager;
        $this->receivableManager = $receivableManager;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $shareEvents = $events->getSharedManager();

        //将支付信息存入付款表，验收采购入库，直接入库
        $this->listeners[] = $shareEvents->attach(
            'Purchase\Controller\WarehouseOrderController', 'warehouse-order.add.post', [$this, 'onAddPayable']
        );
        //将支付信息存入付款表，待入库单入库
        $this->listeners[] = $shareEvents->attach(
            'Purchase\Controller\WarehouseOrderController', 'warehouse-order.insert.post', [$this, 'onAddPayable']
        );

        //当销售订单确认收货
        $this->listeners[] = $shareEvents->attach(
            'Sales\Controller\SalesSendOrderController', 'send-order.finish.post', [$this, 'onAddReceivable']
        );

    }
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            $events->detach($listener);
            unset($this->listeners[$index]);
        }
    }

    /**
     * 添加付款信息
     * @param Event $event
     */
    public function onAddPayable(Event $event)
    {
        $warehouseOrder = $event->getParams();
        if($warehouseOrder->getWarehouseOrderState() == 3) {//只有当入库时，才会进行处理
            $this->payableManager->addPayable($warehouseOrder);
        }
    }

    /**
     * 添加收款信息
     * @param Event $event
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onAddReceivable(Event $event)
    {
        $salesSendOrder = $event->getParams();
        $this->receivableManager->addReceivable($salesSendOrder);
    }
}