<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-4-29
 * Time: 下午8:47
 */

class RouteControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->seedDB();
        $this->testUser = $this->getTestUser();
    }

    /**
     * 测试方法： index
     * 用例描述： 应该返回已登录用户所创建的路线
     */
    public function testIndexMine()
    {
        $requestData['type'] = 'mine';

        $resp = $this->callWantJson('get', 'api/route/', $requestData);
        $this->assertJsonResponse($resp);

        // 测试基本字段
        $respData = $resp->getData(true);
        $keys = ['_id', 'name', 'description', 'status', 'created_at', 'tag'];
        $this->arrayMustHasKeys(head($respData), $keys, true); // 按当前数据填充，$respData必定至少有一个元素

        // 测试创建者的id是否匹配
        $oneRoute = \App\Route::findOrFail(head($respData)['_id']);
        $this->assertEquals($this->testUser['_id'], $oneRoute['creator_id']);

        // 测试不应该存在的字段
        $this->assertArrayNotHasKey('creator', $respData);
        $this->assertArrayNotHasKey('creator_id', $respData);
    }

    /**
     * 测试方法：index
     * 用例描述：测试按景点查情况下的列表返回情况
     */
    public function testIndexSight()
    {
        $requestData['type'] = 'sight';
        $requestData['query'] = '北京';

        $resp = $this->callWantJson('get', 'api/route/', $requestData);
        $this->assertJsonResponse($resp);

        $respData = $resp->getData(true);
        $this->assertCount(2, $respData); //当前测试数据结果集中存在两个匹配

        // 测试返回字段
        $keys = ['_id', 'name', 'description', 'created_at', 'creator_id', 'creator', 'tag'];
        $this->arrayMustHasKeys(head($respData), $keys, true); // 按当前数据填充，$respData必定至少有一个元素

        // 测试创建者数据
        $creatorKey = ['_id', 'username', 'cellphone_number', 'head_image'];
        $this->arrayMustHasEqualKeyValues($this->testUser->toArray(), head($respData)['creator'], $creatorKey);

        // 测试不应该存在的字段
        $this->assertArrayNotHasKey('status', $respData);
    }

    /**
     * 测试方法： index
     * 用例描述： 应该返回所有用户所创建的路线
     */
    public function testIndexLatest()
    {
        $requestData['type'] = 'latest';

        $resp = $this->callWantJson('get', 'api/route/', $requestData);
        $this->assertJsonResponse($resp);

        // 测试基本字段
        $respData = $resp->getData(true);
        $keys = ['_id', 'name', 'description', 'created_at', 'creator_id', 'creator', 'tag'];
        $this->arrayMustHasKeys(head($respData), $keys, true); // 按当前数据填充，$respData必定至少有一个元素

        // 测试创建者数据
        $creatorKey = ['_id', 'username', 'cellphone_number', 'head_image'];
        $this->arrayMustHasKeys(head($respData)['creator'], $creatorKey);

        // 测试数据总数是否正确
        $this->assertEquals(count($respData), \App\Route::count());

        // 测试不应该存在的字段
        $this->assertArrayNotHasKey('status', $respData);

        // 测试默认值
        $resp = $this->callWantJson('get', 'api/route');
        $this->assertJsonResponse($resp);

        // 测试基本字段
        $respData = $resp->getData(true);
        $keys = ['_id', 'name', 'description', 'created_at', 'tag'];
        $this->arrayMustHasKeys(head($respData), $keys, true); // 按当前数据填充，$respData必定至少有一个元素

        // 测试创建者的id是否匹配
        $oneRoute = \App\Route::findOrFail(head($respData)['_id']);
        $this->assertEquals($this->testUser['_id'], $oneRoute['creator_id']);

        // 测试不应该存在的字段
        $this->assertArrayNotHasKey('status', $respData);
    }

    /**
     * 测试方法：index
     * 用例描述：按关键字查询路线，匹配路线的名称和描述
     */
    public function testIndexKeyWord()
    {
        $queryData['type'] = 'keyword';
        $queryData['query'] = '广州';

        $resp = $this->callWantJson('get', 'api/route', $queryData);
        $this->assertJsonResponse($resp);

        $respData = $resp->getData(true);
        $this->assertCount(3, $respData);   //按照当前的填充数据是有3个复合要求的数据
        
        // 测试基本字段
        $oneItem = head($respData);
        $this->arrayMustHasKeys($oneItem, [
            '_id', 'name', 'creator_id', 'created_at', 'creator'
        ], true);

        // 测试不应该存在的字段
        $this->assertArrayNotHasKey('status', $respData);

        // 测试没有关键字匹配项时的情况
        $queryData['query'] = '没有这个关键字';
        $resp = $this->callWantJson('get', 'api/route', $queryData);
        $this->assertJsonResponse($resp);
        $this->assertEmpty($resp->getData(true));   //根据填充数据，此时结果为空

        // 测试创建者数据
        $creatorKey = ['_id', 'username', 'cellphone_number', 'head_image'];
        $this->arrayMustHasEqualKeyValues($this->testUser->toArray(), head($respData)['creator'], $creatorKey);

        // 测试不应该存在的字段
        $this->assertArrayNotHasKey('status', $respData);
    }

    /**
     * 测试方法：index
     * 用例描述：按路线标签查询，匹配路线的标签
     */
    public function testIndexTag()
    {
        $requestData['type'] = 'tag';

        // 测试默认值
        $resp = $this->callWantJson('get', 'api/route', $requestData);
        $this->assertJsonResponse($resp);
        $this->assertEmpty($resp->getData(true));

        $requestData['query'] = 'entertaining';

        // 测试标签
        $resp = $this->callWantJson('get', 'api/route', $requestData);
        $this->assertJsonResponse($resp);

        // 测试基本字段
        $respData = $resp->getData(true);
        $keys = ['_id', 'name', 'creator_id', 'description', 'created_at', 'creator', 'tag'];
        $this->arrayMustHasKeys(head($respData), $keys, true); // 按当前数据填充，$respData必定至少有一个元素

        // 测试创建者数据
        $creatorKeys = ['_id', 'username', 'cellphone_number', 'head_image'];
        $this->arrayMustHasKeys(head($respData)['creator'], $creatorKeys, true);

        $this->assertEquals(3, count($respData)); //按照当前的填充数据是有3个复合要求的数据

        // 测试不应该存在的字段
        $this->assertArrayNotHasKey('status', $respData);
    }

    /**
     * 测试方法：index
     * 用例描述：应返回已登录用户的路线列表，需要进行分页
     */
    public function testIndexPaginateMine()
    {
        $requestData['type'] = 'mine';
        $requestData['per_page'] = 2;
        $requestData['offset'] = 1;

        $resp = $this->callWantJson('get', 'api/route/', $requestData);
        $this->assertJsonResponse($resp);

        // 测试分页字段
        $respData = $resp->getData(true);
        $keys = ['total', 'per_page', 'current_page','last_page', 'from', 'to', 'data'];
        $this->arrayMustHasKeys($respData, $keys, true);
        $this->assertEquals($requestData['per_page'], count($respData['data'])); // 按当前数据填充，$respData['data']必定有至少一个元素

        // 测试数据字段
        $routeData = \App\Route::where('creator_id', $this->testUser['_id'])->get()->toArray();
        $dataKeys = ['_id', 'name', 'description', 'status', 'created_at'];
        $this->arrayMustHasEqualKeyValues(head($respData['data']), $routeData[ $requestData['offset'] ], $dataKeys);

        // 测试不应该存在的字段
        $this->assertArrayNotHasKey('creator', $respData);
        $this->assertArrayNotHasKey('creator_id', $respData);
    }

    /**
     * 测试方法：index
     * 用例描述：应返回所有用户的路线列表，需要进行分页
     */
    public function testIndexPaginateLatest()
    {
        $requestData['per_page'] = 1;
        $requestData['offset'] = 3;
        $requestData['type'] = 'latest';

        $resp = $this->callWantJson('get', 'api/route/', $requestData);
        $this->assertJsonResponse($resp, 200);

        // 测试分页字段
        $respData = $resp->getData(true);
        $keys = ['total', 'per_page', 'current_page','last_page', 'from', 'to', 'data'];
        $this->arrayMustHasKeys($respData, $keys, true);
        $this->assertEquals($requestData['per_page'], count($respData['data'])); // 按当前数据填充，$respData['data']必定有至少一个元素

        // 测试数据字段
        $routeData = \App\Route::all()->toArray();
        $dataKeys = ['_id', 'name', 'description', 'creator_id', 'creator', 'created_at', 'tag'];
        $this->arrayMustHasKeys(head($respData['data']), $dataKeys);
        $dataKeys = ['_id', 'name', 'description', 'creator_id', 'created_at'];
        $this->arrayMustHasEqualKeyValues(head($respData['data']), $routeData[ $requestData['offset'] ], $dataKeys);

        // 测试不应该存在的字段
        $this->assertArrayNotHasKey('status', $respData);
    }

    /**
     * 测试方法：index
     * 用例描述：按关键字查询路线，匹配路线的名称和描述，返回数据需要分页
     */
    public function testIndexPaginateKeyWord()
    {
        $queryData['type'] = 'keyword';
        $queryData['query'] = '广州';

        $queryData['per_page'] = 1;
        $queryData['offset'] = 0;

        $resp = $this->callWantJson('get', 'api/route', $queryData);
        $this->assertJsonResponse($resp);

        $respData = $resp->getData(true);

        // 测试分页字段
        $keys = ['total', 'per_page', 'current_page','last_page', 'from', 'to', 'data'];
        $this->arrayMustHasKeys($respData, $keys, true);
        $this->assertEquals($queryData['per_page'], count($respData['data'])); // 按当前数据填充，$respData['data']必定有至少一个元素

        // 测试数据字段
        $routeData = $respData['data'];
        $this->assertEquals(1, count($routeData)); // 按当前数据填充，应该有1条符合条件的数据
        $keys = ['_id', 'name', 'creator_id', 'description', 'tag', 'created_at', 'creator'];
        $this->arrayMustHasKeys(head($routeData), $keys);

        // 测试不应该存在的字段
        $this->assertArrayNotHasKey('status', $respData);
    }

    /**
     * 测试方法：index
     * 用例描述：按路线标签进行查询，返回数据需要分页
     */
    public function testIndexPaginateTag()
    {
        $queryData['type'] = 'tag';
        $queryData['query'] = 'eating';

        $queryData['per_page'] = 1;
        $queryData['offset'] = 0;

        $resp = $this->callWantJson('get', 'api/route', $queryData);
        $this->assertJsonResponse($resp);
        $respData = $resp->getData(true);

        // 测试分页字段
        $keys = ['total', 'per_page', 'current_page','last_page', 'from', 'to', 'data'];
        $this->arrayMustHasKeys($respData, $keys, true);
        $this->assertEquals($queryData['per_page'], count($respData['data'])); // 按当前数据填充，$respData['data']必定有至少一个元素

        // 测试数据字段
        $routeData = $respData['data'];
        $this->assertEquals(1, count($routeData)); // 按当前数据填充，应该有1条符合条件的数据
        $keys = ['_id', 'name', 'creator_id', 'description', 'tag', 'created_at', 'creator'];
        $this->arrayMustHasKeys(head($routeData), $keys);

        // 测试不应该存在的字段
        $this->assertArrayNotHasKey('status', $respData);
    }

    /**
     * 测试方法： show
     * 用例描述：用户查看路线信息，操作失败
     */
    public function testShowInvalid()
    {
        // 测试路线id不存在的情况
        $resp = $this->callWantJson('get', 'route/9999');
        $this->assertJsonResponse($resp, 404);
    }

    /**
     * 测试方法：show
     * 用例描述：用户查看非公开的路线，操作成功
     */
    public function testShowNotPublicRight()
    {
        $testRoute = \App\Route::where('isPublic', false)
            ->where('creator_id', Auth::user()['_id'])
            ->firstOrFail();

        // 测试创建者查询
        $resp = $this->callWantJson('get', 'route/'. $testRoute['_id']);
        $this->assertJsonResponse($resp);
        $respData = $resp->getData(true);
        $this->assertEquals(Auth::user()['_id'], $respData['creator_id']);

        // 测试返回数据和字段
        $keys = ['_id', 'name', 'description', 'status', 'creator_id', 'created_at', 'isPublic'];
        $this->arrayMustHasEqualKeyValues($testRoute->toArray(), $respData, $keys);
        $keys = array_merge($keys, ['photo', 'transportation']);
        $this->arrayMustHasKeys($respData, $keys, true);

        // 测试创建者的数据 
        $creatorKeys = ['_id', 'username', 'cellphone_number', 'head_image'];
        $this->arrayMustHasKeys($respData['creator'], $creatorKeys);

        // 测试非创建者查询
        $otherUser = \App\User::all()->toArray()[1];
        $this->be( \App\User::findOrFail($otherUser['_id']) );
        $resp = $this->callWantJson('get', 'route/'. $testRoute['_id']);
        $this->assertJsonResponse($resp);
        $respData = $resp->getData(true);
        $this->assertNotEquals(Auth::user()['_id'], $respData['creator_id']);

        // 测试返回数据和字段
        $keys = ['_id', 'name', 'description', 'creator_id', 'created_at'];
        $this->arrayMustHasEqualKeyValues($testRoute->toArray(), $respData, $keys);
        $keys = array_merge($keys, ['transportation']);
        $this->arrayMustHasKeys($respData, $keys, true);

        // 暂定photo字段是会受到是否公开的影响
        $this->assertArrayNotHasKey('photo', $respData);

        // 测试创建者的数据
        $creatorKeys = ['_id', 'username', 'cellphone_number', 'head_image'];
        $this->arrayMustHasKeys($respData['creator'], $creatorKeys);
    }

    /**
     * 测试方法： show
     * 用例描述：用户查看公开的路线信息，操作成功
     */
    public function testShowPublicRight()
    {
        $testRoute = \App\Route::where('isPublic', true)
            ->where('creator_id', Auth::user()['_id'])
            ->firstOrFail();

        // 测试创建者查询
        $resp = $this->callWantJson('get', 'route/'. $testRoute['_id']);
        $this->assertJsonResponse($resp);
        $respData = $resp->getData(true);
        $this->assertEquals(Auth::user()['_id'], $respData['creator_id']);
        
        // 测试返回数据和字段
        $keys = ['_id', 'name', 'description', 'status', 'creator_id', 'created_at', 'isPublic'];
        $this->arrayMustHasEqualKeyValues($testRoute->toArray(), $respData, $keys);
        $keys = array_merge($keys, ['photo', 'transportation']);
        $this->arrayMustHasKeys($respData, $keys, true);

        // 测试创建者的数据
        $creatorKeys = ['_id', 'username', 'cellphone_number', 'head_image'];
        $this->arrayMustHasKeys($respData['creator'], $creatorKeys);

        // 测试非创建者查询
        $otherUser = \App\User::all()->toArray()[1];
        $this->be( \App\User::findOrFail($otherUser['_id']) );
        $resp = $this->callWantJson('get', 'route/'. $testRoute['_id']);
        $this->assertJsonResponse($resp);
        $respData = $resp->getData(true);
        $this->assertNotEquals(Auth::user()['_id'], $respData['creator_id']);

        // 测试返回数据和字段
        $keys = ['_id', 'name', 'description', 'creator_id', 'created_at'];
        $this->arrayMustHasEqualKeyValues($testRoute->toArray(), $respData, $keys);
        $keys = array_merge($keys, ['photo', 'transportation']); // 因为该路线公开，所以应返回所有字段
        $this->arrayMustHasKeys($respData, $keys, true);

        // 测试创建者的数据
        $creatorKeys = ['_id', 'username', 'cellphone_number', 'head_image'];
        $this->arrayMustHasKeys($respData['creator'], $creatorKeys);
    }

    /**
     * 测试方法：store
     * 用例描述：用户新建路线，数据验证失败
     */
    public function testStoreInvalid()
    {
        // 测试提交数据为空的情况
        $resp = $this->callWantJson('post', 'route');
        $this->assertJsonResponse($resp, 400);
    }

    /**
     * 测试方法：store
     * 用例描述：用户新建路线，新建成功
     */
    public function testStoreRight()
    {
        $postData['name'] = 'This is a test route';
        $postData['description'] = 'a wonderful route';
        $resp = $this->callWantJson('post', 'route', $postData);
        $this->assertJsonResponse($resp, 201);

        $respData = $resp->getData(true);

        // 测试返回字段
        $keys = ['_id', 'name', 'status', 'creator_id', 'daily', 'transportation', 'description', 'isPublic', 'tag'];
        $this->arrayMustHasKeys($respData, $keys);

        // 测试创建者的id
        $this->assertEquals($this->testUser['_id'], $respData['creator_id']);

        // 测试返回数据
        $newRoute = \App\Route::findOrFail($respData['_id']);
        $keys = ['_id', 'name', 'status', 'isPublic', 'description', 'creator_id'];
        $this->arrayMustHasEqualKeyValues($newRoute->toArray(), $respData, $keys);
    }

    /**
     * 测试方法： update
     * 用例描述：用户更新路线，更新失败
     */
    public function testUpdateInvalid()
    {
        // 测试路线不存在
        $resp = $this->callWantJson('put', 'route/999');
        $this->assertJsonResponse($resp, 404);

        // 测试无效的状态字段值
        $putData = ['status' => 'wrong status'];
        $this->tryUpdateInvalid($putData);

        // 测试无效的公开标识字段值
        $putData = ['isPublic' => 'Not a boolean'];
        $this->tryUpdateInvalid($putData);

        // 测试无效的路线标签值
        $putData = ['tag' => ['label' => 'eating']];
        $this->tryUpdateInvalid($putData);

        // 测试不存在的路线标签
        $putData = ['tag' => ['label' => 'wrong', 'name' => '没这标签']];
        $this->tryUpdateInvalid($putData);

        // 测试非创建者修改路线
        $otherUser = \App\User::all()->toArray()[1];
        $this->be( \App\User::findOrFail($otherUser['_id']) );
        $testRoute = \App\Route::firstOrFail();
        $resp = $this->callWantJson('put', 'route/'. $testRoute['_id']);
        $this->assertJsonResponse($resp, 404);
        $this->assertArrayHasKey('error', $resp->getData(true));
    }

    /**
     * 辅助方法，尝试更新路线，操作是失败的
     *
     * @param array $putData 更新的数据
     */
    protected function tryUpdateInvalid($putData)
    {
        $testRoute = \App\Route::firstOrFail();
        $resp = $this->callWantJson('put', 'route/'. $testRoute['_id'], $putData);
        $this->assertJsonResponse($resp, 400);
        $keys = ['error', 'data'];
        $respData = $resp->getData(true);
        $this->arrayMustHasKeys($respData, $keys);
    }

    /**
     * 测试方法： update
     * 用例描述：用户更新路线，操作时成功的
     */
    public function testUpdateRight()
    {
        $putData['name'] = 'New name';
        $putData['description'] = 'New description';
        $putData['status'] = 'finished';
        $putData['isPublic'] = true;
        $putData['tag'] = ['label' => 'eating', 'name' => '美食'];
        $testRoute = \App\Route::firstOrFail();
        $resp = $this->callWantJson('put', 'route/'. $testRoute['_id'], $putData);
        $this->assertJsonResponse($resp);

        $respData = $resp->getData(true);
        $updatedRoute = \App\Route::findOrFail($testRoute['_id'])->toArray();

        // 测试返回数据
        $keys = array_keys($putData);
        $this->arrayMustHasEqualKeyValues($updatedRoute, $putData, $keys);

        // 测试返回字段
        array_merge($keys, ['_id', 'creator_id', 'creator', 'daily', 'transportation', 'created_at']);
        $this->arrayMustHasKeys($respData, $keys);
        
        // 测试 creator 字段
        $creatorKeys = ['_id', 'username', 'cellphone_number', 'head_image'];
        $this->arrayMustHasKeys($respData['creator'], $creatorKeys);

        // 测试 tag 字段
        $tagKeys = ['name', 'label'];
        $this->arrayMustHasEqualKeyValues($respData['tag'], $putData['tag'], $tagKeys);
    }

    /**
     * 测试方法：destroy
     * 用例描述：用户删除某一路线，删除失败
     */
    public function testDestroyInvalid()
    {
        // 测试路线不存在
        $resp = $this->callWantJson('delete', 'route/999');
        $this->assertJsonResponse($resp, 404);

        // 测试非路线创建者删除路线
        $otherUserId = \App\User::all()->toArray()[1];
        $this->be( \App\User::findOrFail($otherUserId['_id']) );
        $testRoute = \App\Route::firstOrFail();
        $resp = $this->callWantJson('delete', 'route/'. $testRoute['_id']);
        $this->assertJsonResponse($resp, 404);
    }

    /**
     * 测试方法：destroy
     * 用例描述：用户删除某一路线，删除成功
     */
    public function testDestroyRight()
    {
        $route = \App\Route::firstOrFail();
        $count['old'] = \App\Route::count();
        $resp = $this->callWantJson('delete', 'route/'. $route['_id']);
        $this->assertJsonResponse($resp);

        // 对比删除前后的记录总条数
        $count['new'] = \App\Route::count();
        $this->assertEquals($count['new'] + 1, $count['old']);

        // 测试该路线的景点关联是否被删除
        $linkSightCount = \App\Sight::where('routes', $route['_id'])->count();
        $this->assertEquals(0, $linkSightCount);

        // 强制查询被软删除的数据进行断言
        $trashedRoute = \App\Route::withTrashed()->findOrFail($route['_id']);
        $keys = ['name', 'name', 'creator_id', 'status'];
        $this->arrayMustHasEqualKeyValues($route->toArray(), $trashedRoute->toArray(), $keys);
    }

    /**
     * 执行基本的数据库填充
     */
    protected function seedDB()
    {
        $this->seed('UserTableTestSeeder');
        $this->seed('CollectionTableTestSeeder');
        $this->seed('SightTableTestSeeder');
        $this->seed('RoutesTableTestSeeder');
        $this->seed('RouteTagTableSeeder');
    }

    private $testUser;
}