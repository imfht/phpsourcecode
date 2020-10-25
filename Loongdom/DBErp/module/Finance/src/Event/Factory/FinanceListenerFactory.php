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

namespace Finance\Event\Factory;

use Finance\Event\FinanceListener;
use Finance\Service\PayableManager;
use Finance\Service\ReceivableManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class FinanceListenerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager  = $container->get('doctrine.entitymanager.orm_default');
        $payableManager = $container->get(PayableManager::class);
        $receivableManager = $container->get(ReceivableManager::class);

        return new FinanceListener($entityManager, $payableManager, $receivableManager);
    }
}