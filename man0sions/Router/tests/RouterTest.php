<?php
/**
 * Created by PhpStorm.
 * @author Luficer.p <81434146@qq.com>
 * Date: 16/9/26
 * Time: 上午11:45
 */

namespace LuciferP\Router\test;

define("BASE_PATH", __DIR__);


use LuciferP\Router\Base\RouterFactory;

class RouterTest extends \PHPUnit_Framework_TestCase
{
   public function testRouterResponse(){

      $router = RouterFactory::getRouter();
      $router->setTestRequestData('method','GET');
      $router->setTestRequestData('uri','/home');
      $unit = $this;
      $router->get('/home', function ($req,$res) use($unit) {
          $res->test=true;
          $response = $res->send("hello");
          $unit->assertEquals("hello",$response);
      });


      $router->run();
   }
}
