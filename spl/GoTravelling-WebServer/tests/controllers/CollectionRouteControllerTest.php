<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-6-11
 * Time: 上午10:52
 */
class CollectionRouteControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');

        $this->seedDB();
        // 根据当前数据填充，只有第二位用户才有收藏的路线数据
        $secondUser = \App\User::all()[1];
        $this->be($secondUser);
    }

    /**
     * 测试方法：index
     * 用例描述：用户获取路线收藏列表，操作成功
     */
    public function testIndexRight()
    {
        $resp = $this->callWantJson('get', 'api/collection-route');
        $this->assertJsonResponse($resp);
        $respData = $resp->getData(true);

        // 按当前数据填充，应该有3条符合的数据
        $this->assertEquals(3, count($respData));

        // 检查返回字段和数据
        $firstData = head($respData);
        $targetRoute = \App\Route::findOrFail($firstData['route_id']);
        $this->assertEquals($firstData['owner_id'], Auth::user()['_id']);
        $keys = ['name', 'description', 'creator_id'];
        $this->arrayMustHasEqualKeyValues($firstData, $targetRoute->toArray(), $keys);
    }

    /**
     * 测试方法：show
     * 用例描述：用户查看路线收藏，操作是失败的
     */
    public function testShowInvalid()
    {
        // 测试不存在的路线收藏
        $resp = $this->callWantJson('get', 'api/collection-route/9999');
        $this->assertJsonResponse($resp, 404);
        $this->assertArrayHasKey('error', $resp->getData(true));

        // 测试非收藏者查看路线收藏
        $this->be( \App\User::firstOrFail() );
        $targetCollection = \App\CollectionRoute::firstOrFail();
        $resp = $this->callWantJson('get', 'api/collection-route/'. $targetCollection['_id']);
        $this->assertJsonResponse($resp, 403);
        $this->assertArrayHasKey('error', $resp->getData(true));
    }

    /**
     * 测试方法：show
     * 用例描述：用户查看某一路线收藏，操作是成功的
     */
    public function testShowRight()
    {
        $targetCollection = \App\CollectionRoute::firstOrFail();

        $resp = $this->callWantJson('get', 'api/collection-route/'. $targetCollection['_id']);
        $this->assertJsonResponse($resp);

        // 检查返回字段和数据
        $respData = $resp->getData(true);
        $keys = ['_id', 'owner_id', 'creator_id', 'name', 'description', 'route_id'];
        $this->arrayMustHasEqualKeyValues($respData, $targetCollection->toArray(), $keys);

        // 检查路线的创建者的信息
        $this->assertArrayHasKey('creator', $respData);
        $targetCreator = \App\User::findOrFail($respData['creator']['_id'])->toArray();
        $creatorKeys = ['_id', 'username', 'cellphone_number', 'head_image'];
        $this->arrayMustHasEqualKeyValues($respData['creator'], $targetCreator, $creatorKeys);
    }

    /**
     * 测试方法：store
     * 用例描述：用户新建路线收藏，操作是失败的
     */
    public function testStoreInvalid()
    {
        // 测试无效的路线id
        $postData['route_id'] = 1;
        $resp = $this->callWantJson('post', 'api/collection-route', $postData);
        $this->assertJsonResponse($resp, 400);
        $keys = ['error', 'data'];
        $this->arrayMustHasKeys($resp->getData(true), $keys, true);

        // 测试收藏路线的创建者为收藏者自己的情况
        $testRoute = \App\Route::firstOrFail();
        $postData['route_id'] = $testRoute['_id'];
        $this->be( \App\User::findOrFail($testRoute['creator_id']) );
        $resp = $this->callWantJson('post', 'api/collection-route', $postData);
        $this->assertJsonResponse($resp, 403);
        $this->assertArrayHasKey('error', $resp->getData(true));
    }

    /**
     * 测试方法：store
     * 用例描述：用户新建路线收藏，操作是成功的
     */
    public function testStoreRight()
    {
        $postData['route_id'] = head(\App\Route::getRoutesOnKeyword('北京游'))['_id'];

        $count['old'] = \App\CollectionRoute::count();

        $resp = $this->callWantJson('post', 'api/collection-route', $postData);
        $this->assertJsonResponse($resp, 201);

        // 检查是否创建了新的数据
        $count['new'] = \App\CollectionRoute::count();
        $this->assertEquals($count['old'] + 1, $count['new']);

        // 测试返回字段
        $respData = $resp->getData(true);
        $keys = ['_id', 'route_id', 'owner_id', 'creator_id', 'name', 'description', 'created_at'];
        $this->arrayMustHasKeys($respData, $keys, true);

        //检查路线信息是否正确
        $targetRoute = \App\Route::findOrFail($respData['route_id']);
        $keys = ['name', 'description', 'creator_id'];
        $this->arrayMustHasEqualKeyValues($respData, $targetRoute->toArray(), $keys);
    }

    /**
     * 测试方法：destroy
     * 用例描述：用户删除路线收藏，操作是失败的
     */
    public function testDestroyInvalid()
    {
        // 测试路线收藏不存在的情况
        $resp = $this->callWantJson('delete', 'api/collection-route/9999');
        $this->assertJsonResponse($resp,404);
        $this->assertArrayHasKey('error', $resp->getData(true));

        // 测试非收藏者删除路线收藏
        $this->be( \App\User::firstOrFail() );
        $targetCollection = \App\CollectionRoute::firstOrFail();
        $resp = $this->callWantJson('delete', 'api/collection-route/'. $targetCollection['_id']);
        $this->assertJsonResponse($resp, 403);
        $this->assertArrayHasKey('error', $resp->getData(true));
    }

    /**
     * 测试方法：destroy
     * 用例描述：用户删除路线收藏，操作是成功的
     */
    public function testDestroyRight()
    {
        $targetCollection = \App\CollectionRoute::firstOrFail();

        $count['old'] = \App\CollectionRoute::count();

        $resp = $this->callWantJson('delete', 'api/collection-route/'. $targetCollection['_id']);
        $this->assertJsonResponse($resp);

        // 检查是否成功删除了收藏
        $count['new'] = \App\CollectionRoute::count();
        $this->assertEquals($count['old'] - 1, $count['new']);
    }


    public function seedDB()
    {
        $this->seed('UserTableTestSeeder');
        $this->seed('SightTableTestSeeder');
        $this->seed('RoutesTableTestSeeder');
        $this->seed('CollectionRouteTableTestSeeder');
    }
}