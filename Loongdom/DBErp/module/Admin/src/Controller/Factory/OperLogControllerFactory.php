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

namespace Admin\Controller\Factory;

use Admin\Controller\OperLogController;
use Admin\Service\OperlogManager;
use Interop\Container\ContainerInterface;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\Factory\FactoryInterface;

class OperLogControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $translator         = $container->get(Translator::class);
        $entityManager      = $container->get('doctrine.entitymanager.orm_default');
        $operLogManager     = $container->get(OperlogManager::class);

        return new OperLogController($translator, $entityManager, $operLogManager);
    }
}