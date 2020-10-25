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

namespace Admin;

use Admin\Controller\IndexController;
use Admin\Service\AuthManager;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Zend\Session\SaveHandler\DbTableGateway;
use Zend\Session\SaveHandler\DbTableGatewayOptions;
use Zend\Session\SessionManager;
use Zend\Validator\AbstractValidator;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $event)
    {
        $eventManager = $event->getApplication()->getEventManager();
        //错误日志记录
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onError'], 99);
        //检查管理员权限
        $sharedEventManager = $eventManager->getSharedManager();
        $sharedEventManager->attach(AbstractActionController::class, 'dispatch', [$this, 'checkAdminFilterAccess'], 99);

        //第一时间启用会话配置
        $sessionManager = $event->getApplication()->getServiceManager()->get(SessionManager::class);

        $sessionManager->start();

    }

    /**
     * 权限检查
     * @param MvcEvent $event
     * @return mixed
     */
    public function checkAdminFilterAccess(MvcEvent $event)
    {
        $controller     = $event->getTarget();
        $controllerName = $event->getRouteMatch()->getParam('controller', null);

        //如果是api模块，不进行后台权限验证
        if(substr($controllerName, 0, strpos($controllerName, '\\')) == 'Api') {
            return true;
        }

        $actionName     = $event->getRouteMatch()->getParam('action', null);
        $actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));

        $authManager= $event->getApplication()->getServiceManager()->get(AuthManager::class);

        if($controllerName != IndexController::class) {
            $state = $authManager->filterAccess($controllerName, $actionName);
            if($state == -1) {
                return $controller->redirect()->toRoute('login');
            }elseif($state == -2) {
                return $controller->redirect()->toRoute('home/default', ['action' => 'notAuthorized']);
            }
        }

        return true;
    }

    /**
     * 错误日志记录
     * @param MvcEvent $event
     */
    public function onError(MvcEvent $event)
    {
        $exception = $event->getParam('exception');
        if($exception != null) {
            $exceptionName = $exception->getMessage();
            $file = $exception->getFile();
            $line = $exception->getLine();
            $stackTrace = $exception->getTraceAsString();

            $errorMessage = $event->getError();
            $controllerName = $event->getController();

            $body = '';
            if(isset($_SERVER['REQUEST_URI'])) {
                $body .= "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
            }
            $body .= "Controller: $controllerName\n";
            $body .= "Error message: $errorMessage\n";
            $body .= "Exception: $exceptionName\n";
            $body .= "File: $file\n";
            $body .= "Line: $line\n";
            $body .= "Stack trace:\n" . $stackTrace."\n\n";

            $log     = new Logger();
            $errorLog= new Stream('./data/error/'.date("Y-m-d").'_error.log');
            $log->addWriter($errorLog)->err($body);
        }

    }
}
