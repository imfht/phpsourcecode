<?php

namespace App\Library\Helper;


class ListenerHelper
{
    /**
     * 获取当前访问下的控制器域
     * @param $_controller
     * @return null
     */
    public static function getControllerDomain($_controller)
    {
        $controller_domain = null;
        $parts = explode('\\', $_controller);
        if (isset($parts[2])) {
            $controller_domain = $parts[2];
        }
        return $controller_domain;
    }

    /**
     * User: Gao
     * Date: 2020/3/21
     * Description: 获取当前登录用户
     * @return object|string|null
     */
    public static function getUser()
    {
        $result = null;

        $container = GetterHelper::getContainer();
        if ($container->has('security.token_storage')) {
            $token = $container->get('security.token_storage')->getToken();
            if (!is_null($token)) {
                $result = $token->getUser();
            }
        }

        return $result;
    }
}

?>
