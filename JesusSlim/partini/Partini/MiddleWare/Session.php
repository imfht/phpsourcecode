<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/8/5
 * Time: 下午4:31
 */

namespace Partini\MiddleWare;


use Partini\Application;
use Partini\HttpContext\Context;
use Partini\Session\SessionProviderInterface;

class Session
{

    public function handle(Context $ctx,$next){
        $app = Application::getInstance();
        if($app->getConfig('SESSION_ENABLE') === true){
            $sessionProvider = $app->produce(SessionProviderInterface::class);
            if($sessionProvider){
                session_set_save_handler(
                    array(&$sessionProvider,'open'),
                    array(&$sessionProvider,'close'),
                    array(&$sessionProvider,'read'),
                    array(&$sessionProvider,'write'),
                    array(&$sessionProvider,'destroy'),
                    array(&$sessionProvider,'gc')
                );
                session_start();
            }
        }
        return $next($ctx);
    }

}