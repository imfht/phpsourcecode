<?php


namespace App\EventListener;


use App\Library\Helper\GeneralHelper;
use App\Library\Helper\ListenerHelper;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class AuthBackendListener
{
    protected $event;

    protected $admin_user;

    public function onKernelController(ControllerEvent $event)
    {
        $this->event = $event;

        $this->admin_user = ListenerHelper::getUser();

        $this->main();
    }

    public function main()
    {
        // 校验是否登录
        if (!is_object($this->admin_user)) {
            return;
        }

        // 校验是否是 Backend Controller
        if (!$this->verifyControllerDomain()) {
            return;
        }

        // 校验是否为公共可访问action
        if ($this->isAdminUserGlobalActions()) {
            return;
        }

        // 校验模块访问权限
        if (!$this->verifyAdminModulePermission()) {
            header("Location: /admin/dashboard");
            exit;
        }
    }

    /**
     * User: Gao
     * Date: 2020/3/21
     * Description: 校验是否是 Backend Controller
     * @return bool
     */
    private function verifyControllerDomain(): bool
    {
        $result = false;

        $target_controller_domains = ['Backend'];
        $_controller = $this->event->getRequest()->attributes->get('_controller');
        $controller_domain = ListenerHelper::getControllerDomain($_controller);
        $result = in_array($controller_domain, $target_controller_domains);

        return $result;
    }

    /**
     * User: Gao
     * Date: 2020/3/21
     * Description: 是否为公共方法
     * @return bool
     */
    private function isAdminUserGlobalActions(): bool
    {
        $result = false;

        $given_full_controller = $this->event->getRequest()->attributes->get('_controller');
        $public_full_controllers = [
            'App\Controller\Backend\DefaultController::choiceInfo',
            'App\Controller\Backend\DefaultController::AdminUserInfo',
            'App\Controller\Backend\DefaultController::AdminModule',
            'App\Controller\Backend\DefaultController::showInfoLink',
            'App\Controller\Backend\DefaultController::updateApiDoc',
            'App\Controller\Backend\DefaultController::setCurrentInfo',
            'App\Controller\Backend\DefaultController::DashboardModule',
            'App\Controller\Backend\AdminUserAdminController::profileAction',
        ];
        $result = in_array($given_full_controller, $public_full_controllers);

        return $result;
    }

    /**
     * User: Gao
     * Date: 2020/3/21
     * Description: 模块权限访问校验
     * @return bool
     */
    private function verifyAdminModulePermission(): bool
    {
        $result = true;

        if (is_object($this->admin_user)) {
            $_sonata_admin = $this->event->getRequest()->attributes->get('_sonata_admin');
            $admin_user_sonata_admins = GeneralHelper::getOneInstance()->getModuleSonataAdminsByAdminUserGroupId($this->admin_user->getAdminUserGroupId());
            $result = in_array($_sonata_admin, $admin_user_sonata_admins);
        } else {
            $result = false;
        }

        return $result;
    }
}