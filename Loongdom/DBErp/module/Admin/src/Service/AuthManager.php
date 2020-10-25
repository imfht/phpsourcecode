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

namespace Admin\Service;

use Admin\Controller\HomeController;
use Admin\Entity\AdminUser;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Session\Container;

class AuthManager
{
    const USER_ALREADY_LOGIN    = 1;     //已经登录
    const USER_NOT_LOGIN        = -1;    //未登录
    const USER_NOT_PERMISSION   = -2;       //无权限
    private $authService;

    private $sessionManager;

    private $config;

    public function __construct(
        AuthenticationService $authService,
        $sessionManager,
        $config
    )
    {
        $this->authService      = $authService;
        $this->sessionManager   = $sessionManager;
        $this->config           = $config;
    }

    /**
     * 登录
     * @param $name
     * @param $password
     * @param $rememberMe
     * @return Result
     */
    public function login($name, $password, $rememberMe)
    {
        //不为空，说明已经登录
        if($this->authService->getIdentity() != null) return new Result(Result::SUCCESS, $this->authService->getIdentity(), ['Already logged in']);

        $adminUser = new AdminUser();

        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setName($name);
        $authAdapter->setPassword($password);
        $authAdapter->setAdminState(1);

        $result = $this->authService->authenticate();
        if($result->getCode() == Result::SUCCESS) {
            if($rememberMe) $this->sessionManager->rememberMe($this->config['session_config']['remember_me_seconds']);

            $userInfo   = $authAdapter->getUser();
            $userGroup  = $authAdapter->getUserGroup();
            $session    = new Container('admin', $this->sessionManager);
            $session->admin_id      = $userInfo->getAdminId();
            $session->admin_name    = $userInfo->getAdminName();
            $session->admin_group_id= $userGroup->getAdminGroupId();
            $session->admin_group_name = $userGroup->getAdminGroupName();
            $session->admin_permission = !empty($userGroup->getAdminGroupPurview()) ? explode(',', $userGroup->getAdminGroupPurview()) : [];
        }

        return $result;
    }

    /**
     * 退出操作
     */
    public function logout()
    {
        if ($this->authService->getIdentity()==null) {
            throw new \Exception('The user is not logged in');
        }

        $session = new Container('admin', $this->sessionManager);
        $session->getManager()->getStorage()->clear('admin');

        // Remove identity from session.
        $this->authService->clearIdentity();
    }

    /**
     * 检查是否已经登录
     * @return bool
     */
    public function checkLogin()
    {
        if ($this->authService->getIdentity()==null) return false;
        return true;
    }

    /**
     * 权限验证
     * @param $controllerName
     * @param $actionName
     * @return int
     */
    public function filterAccess($controllerName, $actionName)
    {
        //未登录
        if(!$this->authService->hasIdentity()) {
            return self::USER_NOT_LOGIN;
        }
        $session = new Container('admin', $this->sessionManager);
        //判断该管理员是否存在，如果不存在则进行session清理
        if(!$this->authService->getAdapter()->checkAdmin($this->authService->getIdentity())) {
            $session->getManager()->getStorage()->clear('admin');
            $this->authService->clearIdentity();

            return self::USER_NOT_LOGIN;
        }
        //判断是否有权限(不检查管理员组)
        if(
            $controllerName != HomeController::class
            && $session->admin_group_id != 1
            && !empty($session->admin_permission)
            && !in_array(str_replace('\\', '_', $controllerName).'_'.$actionName, $session->admin_permission)
        ) {
            if(!in_array($actionName,//不需要授权即可访问的
                [
                    'goodsIdSearch',
                    'ajaxGoodsSearch',
                    'ajaxRegion',
                    'createAccessId',
                    'createAccessSecret',
                    'customerIdSearch',
                    'supplierIdSearch'
                ])
            ) return self::USER_NOT_PERMISSION;
        }

        return self::USER_ALREADY_LOGIN;
    }
}