<?php
namespace Core;

use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Security as Psecurity;

class Security extends Psecurity
{

    public $securityHook;
    public $security;
    public $controllerName;
    public $actionName;
    public $module;
    public $securityName;
    public $params;
    public $roles;
    protected $uid;
    public $adopt = false;

    public function getAcl()
    {
        global $di;
        $this->security = Config::get('security');
        $this->securityHook = Config::cache('securityHook');
        $this->roles = $di->getShared('user')->roles;
        $this->uid = $di->getShared('user')->id;
        $this->controllerName = strtolower($di->getShared('dispatcher')->getControllerName());
        $this->actionName = strtolower($di->getShared('dispatcher')->getActionName());
        $this->module = strtolower($di->getShared('router')->getModuleName());
        $this->params = $di->getShared('dispatcher')->getParams();
        $this->securityName = array(
            $this->controllerName,
            $this->module . ':' . $this->controllerName,
            $this->module . ':' . $this->controllerName . ':' . $this->actionName,
        );
        $end = $this->module . ':' . $this->controllerName . ':' . $this->actionName;
        foreach ($this->params as $value) {
            $end = $end . '-' . $value;
            $this->securityName[] = $end;
        }
        $this->securityName = array_reverse($this->securityName);
    }

    public function isCanAccess($access)
    {
        $url = $this->getDI()->getRequest()->getURI();
        if (isset($access['path']) && is_array($access['path']) && !empty($access['path'])) {
            $output = false;
            foreach ($access['path'] as $path) {
                if (preg_match('|' . $path . '|', $url)) {
                    break;
                }
                if($output === false){
                    return false;
                }
            }
        }
        if (isset($access['expath']) && is_array($access['expath']) && !empty($access['expath'])) {
            foreach ($access['expath'] as $path) {
                if (preg_match($path, $url)) {
                    return false;
                }
            }
        }
        if (isset($access['roles']) && is_array($access['roles']) && !empty($access['roles'])) {
            if (!array_intersect($access['roles'], $this->roles)) {
                return false;
            }
        }
        if (isset($access['exroles']) && is_array($access['exroles']) && !empty($access['exroles'])) {
            if (array_intersect($access['roles'], $this->roles)) {
                return false;
            }
        }
        if (isset($access['user']) && is_array($access['user']) && !empty($access['user'])) {
            if(is_string($access['user'])){
                $access['user'] = explode(',',$access['user']);
            }
            if (!array_search($this->uid,$access['roles'])) {
                return false;
            }
        }
        if (isset($access['exuser']) && is_array($access['exuser']) && !empty($access['exuser'])) {
            if(is_string($access['exuser'])){
                $access['exuser'] = explode(',',$access['exuser']);
            }
            if (array_search($this->uid,$access['exuser'])) {
                return false;
            }
        }
        if (isset($access['exroles']) && is_array($access['exroles']) && !empty($access['exroles'])) {
            if (array_intersect($access['roles'], $this->roles)) {
                return false;
            }
        }
        if (isset($access['callable']) && is_array($access['callable']) && !empty($access['callable'])) {
            foreach ($access['callable'] as $callable) {
                if (is_callable($callable)) {
                    if (call_user_func($callable) === false) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function isUrlCanAccess($url)
    {

    }

    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        global $di;
        $this->getAcl();
        foreach ($this->securityName as $sn) {
            foreach ($this->roles as $role => $state) {
                if (isset($this->security[$sn][$role])) {
                    if ($this->security[$sn][$role] === true) {
                        $this->adopt = true;
                        break 2;
                    } elseif ($this->security[$sn][$role] === false) {
                        $this->adopt = false;
                        break 2;
                    }
                    if (isset($this->securityHook[$sn])) {
                        $this->adopt = call_user_func($sn, $this);
                        if ($this->adopt === true) {
                            break 2;
                        } elseif ($this->adopt === false) {
                            break 2;
                        }
                    }
                }
            }
        }
        if ($this->adopt == false) {
            $di->getShared('response')->setStatusCode(404, "Not Found");
            $di->getShared('dispatcher')->forward(array(
                'controller' => 'Index',
                'action' => 'NotFound',
                'module' => 'core',
                'namespace' => 'Modules\Core\Controllers',
            ));
            return false;
        } else {
            return true;
        }
    }
}
