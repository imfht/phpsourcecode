<?php
/**
 * YanPHP
 * User: weilongjiang(江炜隆)<william@jwlchina.cn>
 * Date: 2017/10/3
 * Time: 15:16
 */

namespace TestNamespace;


use Yan\Core\Config;
use Yan\Core\Dispatcher;

class DispatcherTest extends BaseTestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        Config::initialize();
    }

    //TODO 测试用例不应该依赖于Cgi层
    public function testDispatch(){
        $_SERVER['REQUEST_URI'] = '/user/index?param=1';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['PHP_SELF'] = 'interface.php';
        $_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__,2);

        Dispatcher::initialize();

        list($controller,$method) = Dispatcher::dispatch();

        $this->assertEquals('App\Cgi\Controller\UserController',$controller);
        $this->assertEquals('index',$method);
    }
}