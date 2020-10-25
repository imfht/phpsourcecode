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

namespace Store\Controller\Factory;


use Interop\Container\ContainerInterface;
use Store\Controller\PositionController;
use Store\Service\PositionManager;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\Factory\FactoryInterface;

class PositionControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $translator         = $container->get(Translator::class);
        $entityManager      = $container->get('doctrine.entitymanager.orm_default');
        $positionManager    = $container->get(PositionManager::class);

        return new PositionController($translator, $entityManager, $positionManager);
    }
}