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

namespace Stock\Controller\Factory;

use Interop\Container\ContainerInterface;
use Stock\Controller\StockCheckController;
use Stock\Service\StockCheckGoodsManager;
use Stock\Service\StockCheckManager;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\Factory\FactoryInterface;

class StockCheckControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager      = $container->get('doctrine.entitymanager.orm_default');
        $translator         = $container->get(Translator::class);
        $stockCheckManager  = $container->get(StockCheckManager::class);
        $stockCheckGoodsManager = $container->get(StockCheckGoodsManager::class);

        return new StockCheckController($translator, $entityManager, $stockCheckManager, $stockCheckGoodsManager);
    }
}