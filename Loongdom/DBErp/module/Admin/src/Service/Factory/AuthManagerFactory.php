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

namespace Admin\Service\Factory;

use Admin\Service\AuthManager;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;

class AuthManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authService    = $container->get(AuthenticationService::class);
        $sessionManager = $container->get(SessionManager::class);

        $config = $container->get('Config');

        return new AuthManager($authService, $sessionManager, $config);
    }
}