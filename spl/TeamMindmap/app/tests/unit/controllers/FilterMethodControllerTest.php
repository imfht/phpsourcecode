<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-12-28
 * Time: 下午4:30
 */


class FilterMethodControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->controllerObj = $this->app->make('FilterMethodController');
    }

    public function testGetIndex()
    {
        $resp = $this->controllerObj->getIndex('projectDiscussion');
        $this->assertEquals(200, $resp->getStatusCode());

        $respDataArray = $resp->getData(true);
        $keys = ['open', 'user'];
        $this->arrayMustHasKeys($respDataArray, $keys, true);
        $keys = ['cond', 'label'];
        $this->arrayMustHasKeys(head($respDataArray)[0], $keys, true);
    }

    private $controllerObj;
}