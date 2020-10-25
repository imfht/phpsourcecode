<?php

namespace App\Library\Helper;

class GetterHelper
{

    public static function getContainer()
    {
        global $kernel;

        return $kernel->getContainer();
    }

    public static function getSession()
    {
        return self::getContainer()->get('session');
    }

    public static function getRequest()
    {
        return self::getContainer()->get('request_stack')->getCurrentRequest();
    }

    public static function getParameter($key)
    {
        return self::getContainer()->getParameter($key);
    }

    public static function getService($key)
    {
        return self::getContainer()->get($key);
    }

    public static function getEntityManager()
    {
        return self::getService('doctrine.orm.entity_manager');
    }

}
