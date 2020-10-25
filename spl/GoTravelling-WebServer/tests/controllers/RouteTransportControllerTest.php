<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-5-7
 * Time: 下午8:20
 */
class RouteTransportControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');
        $this->seedDB();
        $this->testUser = $this->getTestUser(false);
    }

    /**
     * 测试方法： show
     * 用例描述：用户查询某一交通方式信息，操作失败
     */
    public function testShowInvalid()
    {
        // 测试路线不存在
        $resp = $this->callWantJson('get', 'route/9999/transport/9999');
        $this->assertJsonResponse($resp, 404);

        // 测试交通方式不存在
        $testRoute = \App\Route::firstOrFail();
        $resp = $this->callWantJson('get', 'route/'. $testRoute['_id']. '/transport/9999');
        $this->assertJsonResponse($resp, 404);
    }

    /**
     * 测试方法： show
     * 用例描述：用户查询某一交通方式信息，操作成功
     */
    public function testShowRight()
    {
        $testRoute = \App\Route::firstOrFail();
        $transportId = head($testRoute['transportation'])['_id'];
        $resp = $this->callWantJson('get', 'route/'. $testRoute['_id']. '/transport/'. $transportId);
        $this->assertJsonResponse($resp);
        $respData = $resp->getData(true);
        $newRoute = \App\Route::getTransportData($testRoute['_id'], $transportId);

        // 测试返回数据和字段
        $keys = ['from_name', 'from_sight_id', 'from_loc', 'to_name', 'to_sight_id', 'to_loc',
            'description', 'prize', 'consuming'];
        $this->arrayMustHasKeys($respData, $keys);
        $this->arrayMustHasEqualKeyValues($newRoute, $respData, $keys);
        
        // 测试 loc 字段
        $locKeys = ['type', 'coordinates'];
        $this->arrayMustHasKeys($respData['from_loc'], $locKeys);
        $this->arrayMustHasKeys($respData['to_loc'], $locKeys);
        $this->assertEquals('Point', $respData['from_loc']['type']);
        $this->assertEquals('Point', $respData['to_loc']['type']);

        // 测试 description 字段
        $descKeys = ['type', 'policy'];
        $targetDesc = head($testRoute['transportation'])['description'];
        $this->assertEquals(count($targetDesc['policy']), count($respData['description']['policy']));
        $this->arrayMustHasKeys($respData['description'], $descKeys, true) ;
        $policyKeys = ['label', 'name'];
        $this->arrayMustHasKeys( head($respData['description']['policy']), $policyKeys, true);
    }

    /**
     * 测试方法： store
     * 用例描述：用户新建交通路线，操作失败
     */
    public function testStoreInvalid()
    {
        $postData['from_name'] = '岳麓山风景名胜区';
        $postData['from_sight_id'] = \App\Sight::getSightId('岳麓山风景名胜区');
        $postData['from_loc'] = ['type'=>'Point', 'coordinates' => [22.587092, 113.88849]];
        $postData['to_name'] = '湘府文化公园';
        $postData['to_sight_id'] = \App\Sight::getSightId('湘府文化公园');
        $postData['to_loc'] = ['type'=>'Point', 'coordinates' => [28.194667, 112.943373]];
        $postData['description'] = [
            'type' => 'bus',
            'policy' => ['least_time', 'avoid_subway']
        ];
        $postData['prize'] = 250;
        $postData['consuming'] = 35;

        // 测试提交数据为空
        $testData = [];
        $this->tryStoreInvalid($testData);

        // 测试 from_name 和 from_loc 不同时存在的情况
        $testData = $postData;
        $testData['from_loc'] = ['type'=>'Box', 'coordinates' => [22.587092, 113.88849]];
        $this->tryStoreInvalid($testData);

        // 测试 to_name 和 to_loc 不同时存在的情况
        $testData = $postData;
        $testData['to_loc'] = 'wrong loc data';
        $this->tryStoreInvalid($testData);

        // 测试无效的 loc 格式
        $testData = $postData;
        $testData['to_loc'] = ['type'=>'Point', 'coordinates' => []];
        $this->tryStoreInvalid($testData);

        // 测试无效的 prize 字段值
        $testData = $postData;
        $testData['prize'] = 'not an integer';
        $this->tryStoreInvalid($testData);

        // 测试无效的consuming 字段值
        $testData = $postData;
        $testData['consuming'] = -90;
        $this->tryStoreInvalid($testData);

        // 测试无效的 id 字段
        $testData = $postData;
        $testData['from_sight_id'] = 12;
        $this->tryStoreInvalid($testData);

        // 测试无效的 description 格式
        $testData = $postData;
        $testData['description'] = 'wrong description';
        $this->tryStoreInvalid($testData);

        // 测试非法类型
        $testData = $postData;
        $testData['description'] = ['type' => 'wrong type', 'policy' => ['least_time']];
        $this->tryStoreInvalid($testData);

        // 测试不属于某种方式的策略
        $testData = $postData;
        $testData['description'] = ['type' => 'bus', 'policy' => ['least_distance']];
        $this->tryStoreInvalid($testData);

        // 测试路线不存在
        $resp = $this->callWantJson('post', 'route/9999/transport', $postData);
        $this->assertJsonResponse($resp, 404);
        $this->arrayHasKey('error', $resp->getData(true));

        // 测试非路线创建者新建日程
        $otherUser = \App\User::all()->toArray()[1];
        $this->be( \App\User::findOrFail($otherUser['_id']) );
        $testRoute = \App\Route::firstOrFail();
        $resp = $this->callWantJson('post', 'route/'. $testRoute['_id']. '/transport', $postData);
        $this->assertJsonResponse($resp, 404);
        $this->arrayHasKey('error', $resp->getData(true));
    }

    /**
     * 辅助方法，尝试新建交通方式，操作是失败的
     */
    protected function tryStoreInvalid($postData)
    {
        $testRoute = \App\Route::firstOrFail();
        $resp = $this->callWantJson('post', 'route/'. $testRoute['_id']. '/transport', $postData);
        $this->assertJsonResponse($resp, 400);
        $keys = ['error', 'data'];
        $respData = $resp->getData(true);
        $this->arrayMustHasKeys($respData, $keys);
    }

    /**
     * 测试方法： store
     * 用例描述：用户新建交通方式，操作成功
     */
    public function testStoreRight()
    {
        $postData['from_name'] = '岳麓山风景名胜区';
        $postData['from_sight_id'] = \App\Sight::getSightId('岳麓山风景名胜区');
        $postData['from_loc'] = ['type'=>'Point', 'coordinates' => [22.587092, 113.88849]];
        $postData['to_name'] = '湘府文化公园';
        $postData['to_sight_id'] = \App\Sight::getSightId('湘府文化公园');
        $postData['to_loc'] = ['type'=>'Point', 'coordinates' => [28.194667, 112.943373]];
        $postData['description'] = [
            'type' => 'bus',
            'policy' => ['least_time', 'avoid_subway']
        ];
        $postData['prize'] = 250;
        $postData['consuming'] = 35;

        $testRoute = \App\Route::firstOrFail();
        $resp = $this->callWantJson('post', 'route/'. $testRoute['_id']. '/transport', $postData);
        $this->assertJsonResponse($resp, 201);

        // 测试返回数据和字段
        $keys = ['_id', 'from_name', 'from_sight_id', 'from_loc', 'to_name', 'to_sight_id', 'to_loc',
            'description', 'prize', 'consuming'];
        $respData = $resp->getData(true);
        $newRoute = \App\Route::findOrFail($testRoute['_id']);
        $this->arrayMustHasKeys($respData, $keys);
        $this->arrayMustHasEqualKeyValues(last($newRoute['transportation']), $respData, $keys);
        
        // 对比新建前后的记录总数
        $count['old'] = count($testRoute['transportation']);
        $count['new'] = count($newRoute['transportation']);
        $this->assertEquals($count['old'] + 1, $count['new']);

        // 测试 from/to_sight_id 不存在的情况
        unset($postData['to_sight_id']);
        unset($postData['from_sight_id']);
        $testRoute = \App\Route::firstOrFail();
        $resp = $this->callWantJson('post', 'route/'. $testRoute['_id']. '/transport', $postData);
        $this->assertJsonResponse($resp, 201);

        // 测试返回数据和字段
        $keys = ['_id', 'from_name', 'from_loc', 'to_name', 'to_loc',
            'description', 'prize', 'consuming'];
        $respData = $resp->getData(true);
        $newRoute = \App\Route::findOrFail($testRoute['_id']);
        $this->arrayMustHasKeys($respData, $keys);
        $this->arrayMustHasEqualKeyValues(last($newRoute['transportation']), $respData, $keys);

        // 对比新建前后的记录总数
        $count['old'] = count($testRoute['transportation']);
        $count['new'] = count($newRoute['transportation']);
        $this->assertEquals($count['old'] + 1, $count['new']);

        // 测试 description 字段
        $descKeys = ['type', 'policy'];
        $this->assertEquals(count($postData['description']['policy']), count($respData['description']['policy']));
        $this->arrayMustHasKeys($respData['description'], $descKeys, true) ;
        $policyKeys = ['label', 'name'];
        $this->arrayMustHasKeys( head($respData['description']['policy']), $policyKeys, true);

        // 测试无交通方式策略的情况（或者交通方式为walk时的情况）
        $postData['description'] = ['type' => 'walk'];
        $testRoute = \App\Route::firstOrFail();
        $resp = $this->callWantJson('post', 'route/'. $testRoute['_id']. '/transport', $postData);
        $this->assertJsonResponse($resp, 201);

        // 测试返回数据和字段
        $keys = ['_id', 'from_name', 'from_loc', 'to_name', 'to_loc',
            'description', 'prize', 'consuming'];
        $respData = $resp->getData(true);
        $newRoute = \App\Route::findOrFail($testRoute['_id']);
        $this->arrayMustHasKeys($respData, $keys);
        $this->arrayMustHasEqualKeyValues(last($newRoute['transportation']), $respData, $keys);

        // 对比新建前后的记录总数
        $count['old'] = count($testRoute['transportation']);
        $count['new'] = count($newRoute['transportation']);
        $this->assertEquals($count['old'] + 1, $count['new']);

        // 测试 description 字段
        $descKeys = ['type', 'policy'];
        $this->assertEquals(0, count($respData['description']['policy']));
        $this->arrayMustHasKeys($respData['description'], $descKeys, true) ;
    }

    /**
     * 测试方法： update
     * 用例描述：用户更新交通方式，操作失败
     */
    public function testUpdateInvalid()
    {
        $resp = $this->callWantJson('put', 'route/9999/transport/9999', []);
        $this->assertJsonResponse($resp, 404);

        $testRoute = \App\Route::firstOrFail();
        $resp = $this->callWantJson('put', 'route/'. $testRoute['_id']. '/transport/9999', []);
        $this->assertJsonResponse($resp, 404);

        $transportId = head($testRoute['transportation'])['_id'];
        // 测试 from_name 存在，而 from_loc 不存在
        $putData['from_name'] = '岳麓山风景名胜区';
        $resp = $this->callWantJson('put', 'route/'. $testRoute['_id']. '/transport/'. $transportId, $putData);
        $this->assertJsonResponse($resp, 400);
        $respData = $resp->getData(true);
        $keys = ['error', 'data'];
        $this->arrayMustHasKeys($respData, $keys);

        // 测试 to_loc 存在，而 to_name 不存在
        $putData['to_loc'] = ['type'=>'Point', 'coordinates' => [28.194667, 112.943373]];
        $resp = $this->callWantJson('put', 'route/'. $testRoute['_id']. '/transport/'. $transportId, $putData);
        $this->assertJsonResponse($resp, 400);
        $respData = $resp->getData(true);
        $keys = ['error', 'data'];
        $this->arrayMustHasKeys($respData, $keys);

        // 测试负数验证
        $putData['from_loc'] = ['type'=>'Point', 'coordinates' => [22.587092, 113.88849]];
        $putData['to_name'] = '湘府文化公园';
        $putData['prize'] = -250;
        $resp = $this->callWantJson('put', 'route/'. $testRoute['_id']. '/transport/'. $transportId, $putData);
        $this->assertJsonResponse($resp, 400);
        $respData = $resp->getData(true);
        $keys = ['error', 'data'];
        $this->arrayMustHasKeys($respData, $keys);

        // 测试无效的 description 的格式
        $putData['description'] = 'wrong description';
        $resp = $this->callWantJson('put', 'route/'. $testRoute['_id']. '/transport/'. $transportId, $putData);
        $this->assertJsonResponse($resp, 400);
        $respData = $resp->getData(true);
        $keys = ['error', 'data'];
        $this->arrayMustHasKeys($respData, $keys);

        // 测试非法的类型
        $putData['description'] = ['type' => 'wrong type', 'policy' => ['least_time']];
        $resp = $this->callWantJson('put', 'route/'. $testRoute['_id']. '/transport/'. $transportId, $putData);
        $this->assertJsonResponse($resp, 400);
        $respData = $resp->getData(true);
        $keys = ['error', 'data'];
        $this->arrayMustHasKeys($respData, $keys);

        // 测试不属于某种方式的策略
        $putData['description'] = ['type' => 'drive', 'policy' => ['least_exchange']];
        $resp = $this->callWantJson('put', 'route/'. $testRoute['_id']. '/transport/'. $transportId, $putData);
        $this->assertJsonResponse($resp, 400);
        $respData = $resp->getData(true);
        $keys = ['error', 'data'];
        $this->arrayMustHasKeys($respData, $keys);

        // 测试非创建者更新交通方式
        unset($putData['prize']);
        $otherUser = \App\User::all()->toArray()[1];
        $this->be( \App\User::findOrFail($otherUser['_id']) );
        $resp = $this->callWantJson('put', 'route/'. $testRoute['_id']. '/transport/'. $transportId, $putData);
        $this->assertJsonResponse($resp, 404);
    }

    /**
     * 测试用例： update
     * 用例描述：用户更新交通方式，操作成功
     */
    public function testUpdateRight()
    {
        $putData['from_name'] = '岳麓山风景名胜区';
        $putData['from_sight_id'] = \App\Sight::getSightId('岳麓山风景名胜区');
        $putData['from_loc'] = ['type'=>'Point', 'coordinates' => [22.587092, 113.88849]];
        $putData['to_name'] = '湘府文化公园';
        $putData['to_sight_id'] = \App\Sight::getSightId('湘府文化公园');
        $putData['to_loc'] = ['type'=>'Point', 'coordinates' => [28.194667, 112.943373]];
        $putData['description'] = [
            'type' => 'bus',
            'policy' => ['least_time', 'avoid_subway']
        ];
        $putData['prize'] = 250;
        $putData['consuming'] = 35;

        $testRoute = \App\Route::where('status', 'planning')->first()->toArray(); // 这里选择的测试路线是有交通方式的
        $transportId = head($testRoute['transportation'])['_id'];
        $resp = $this->callWantJson('put', 'route/'.$testRoute['_id']. '/transport/'. $transportId, $putData);
        $this->assertJsonResponse($resp);
        $respData = $resp->getData(true);
        $keys = ['from_name', 'from_sight_id', 'from_loc', 'to_name', 'to_sight_id', 'to_loc',
            'prize', 'consuming'];
        $this->arrayMustHasEqualKeyValues($putData, $respData, $keys);
        $locKeys = ['type', 'coordinates'];
        $this->arrayMustHasKeys($respData['from_loc'], $locKeys);
        $this->arrayMustHasKeys($respData['to_loc'], $locKeys);
        $this->assertEquals('Point', $respData['from_loc']['type']);
        $this->assertEquals('Point', $respData['to_loc']['type']);

        // 测试 description 字段
        $descKeys = ['type', 'policy'];
        $this->assertEquals(count($putData['description']['policy']), count($respData['description']['policy']));
        $this->arrayMustHasKeys($respData['description'], $descKeys, true);
        $policyKeys = ['label', 'name'];
        $this->arrayMustHasKeys( head($respData['description']['policy']), $policyKeys, true);

        // 测试 from_name 和 from_loc 同时不存在的情况
        unset($putData['from_name']);
        unset($putData['from_loc']);
        $resp = $this->callWantJson('put', 'route/'.$testRoute['_id']. '/transport/'. $transportId, $putData);
        $this->assertJsonResponse($resp);
        $respData = $resp->getData(true);
        $keys = ['from_sight_id', 'to_name', 'to_sight_id', 'to_loc', 'prize', 'consuming'];
        $this->arrayMustHasEqualKeyValues($putData, $respData, $keys);
        $locKeys = ['type', 'coordinates'];
        $this->arrayMustHasKeys($respData['to_loc'], $locKeys);
        $this->assertEquals('Point', $respData['to_loc']['type']);

        // 测试 description 字段
        $descKeys = ['type', 'policy'];
        $this->assertEquals(count($putData['description']['policy']), count($respData['description']['policy']));
        $this->arrayMustHasKeys($respData['description'], $descKeys, true);
        $policyKeys = ['label', 'name'];
        $this->arrayMustHasKeys( head($respData['description']['policy']), $policyKeys, true);

        // 测试 to_name 和 to_loc 同时不存在的情况
        unset($putData['to_name']);
        unset($putData['to_loc']);
        $resp = $this->callWantJson('put', 'route/'.$testRoute['_id']. '/transport/'. $transportId, $putData);
        $this->assertJsonResponse($resp);
        $respData = $resp->getData(true);
        $keys = ['from_sight_id', 'to_sight_id', 'prize', 'consuming'];
        $this->arrayMustHasEqualKeyValues($putData, $respData, $keys);

        // 测试 description 字段
        $descKeys = ['type', 'policy'];
        $this->assertEquals(count($putData['description']['policy']), count($respData['description']['policy']));
        $this->arrayMustHasKeys($respData['description'], $descKeys, true);
        $policyKeys = ['label', 'name'];
        $this->arrayMustHasKeys( head($respData['description']['policy']), $policyKeys, true);
    }

    /**
     * 测试方法： destroy
     * 用例描述：用户删除交通方式，操作失败
     */
    public function testDestroyInvalid()
    {
        // 测试路线不存在
        $resp = $this->callWantJson('delete', 'route/9999/transport/99999');
        $this->assertJsonResponse($resp, 404);

        // 测试交通方式不存在
        $testRoute = \App\Route::firstOrFail();
        $resp = $this->callWantJson('delete', 'route/'. $testRoute['_id']. '/transport/99999');
        $this->assertJsonResponse($resp, 404);
    }

    /**
     * 测试方法： destroy
     * 用例描述：用户删除交通方式，操作是成功的
     */
    public function testDestroyRight()
    {
        $testRoute = \App\Route::firstOrFail();
        $transportId = head($testRoute['transportation'])['_id'];

        $resp = $this->callWantJson('delete', 'route/'. $testRoute['_id']. '/transport/'. $transportId);
        $this->assertJsonResponse($resp);
        $newRoute = \App\Route::firstOrFail();

        // 对比删除前后的记录总条数
        $count['old'] = count($testRoute['transportation']);
        $count['new'] = count($newRoute['transportation']);
        $this->assertEquals($count['new'] + 1, $count['old']);
    }

    /**
     * 执行基本的数据库填充
     */
    protected function seedDB()
    {
        $this->seed('UserTableTestSeeder');
        $this->seed('SightTableTestSeeder');
        $this->seed('RoutesTableTestSeeder');
    }

    protected $testUser;
}