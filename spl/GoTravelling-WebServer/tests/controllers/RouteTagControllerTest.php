<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-5-20
 * Time: 下午7:52
 */
class RouteTagControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');
        $this->seed('RouteTagTableSeeder');
    }

    /**
     * 测试方法：getTag
     * 用例描述：返回路线标签的列表，操作成功
     */
    public function testGetTag()
    {
        $resp = $this->callWantJson('get', 'api/route-tag/tag');
        $this->assertJsonResponse($resp);
        $keys = ['_id', 'name', 'label'];
        $tagData = \App\RouteTag::all()->toArray();
        $this->arrayMustHasEqualKeyValues(head($tagData), head($resp->getData(true)), $keys);
    }
}
