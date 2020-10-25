<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-5-3
 * Time: 下午6:16
 */
use App\Route;
use App\Sight;

class RouteDailyControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');

        $this->getTestUser(true);
        $this->seed('SightTableTestSeeder');
        $this->seed('RoutesTableTestSeeder');
    }

    /**
     * 测试方法：show
     * 用例描述：用户查询某一日程信息，查询失败
     */
    public function testShowInvalid()
    {
        // 测试路线不存在
        $resp = $this->callWantJson('get', 'route/1/daily/99');
        $this->assertJsonResponse($resp, 404);

        // 测试日程不存在
        $testRoute = Route::firstOrFail();
        $resp = $this->callWantJson('get', 'route/'. $testRoute['_id']. '/daily/99');
        $this->assertJsonResponse($resp, 404);
    }

    /**
     * 测试方法：show
     * 用例描述：用户查询某一日程信息，查询成功
     */
    public function testShowRight()
    {
        $testRoute = Route::firstOrFail();
        $dailyId = head($testRoute['daily'])['_id'];
        $resp = $this->callWantJson('get', 'route/'. $testRoute['_id']. '/daily/'. $dailyId);
        $this->assertJsonResponse($resp);
        $respData = $resp->getData(true);
        $testDaily = Route::getDailyData($testRoute['_id'], $dailyId);

        // 测试基本字段
        $keys = ['_id', 'remark', 'date', 'sights'];
        $this->arrayMustHasKeys($respData, $keys);
        $this->assertEquals($testDaily['remark'], $respData['remark']);

        // 测试景点数据
        $testSight = head($testDaily['sights']);
        $respSight = head($respData['sights']);
        $keys = ['sights_id', 'name', 'loc'];
        $this->arrayMustHasKeys($respSight, $keys);
        $this->assertEquals($testSight['sights_id'], $respSight['sights_id']);
    }

    /**
     * 测试方法：store
     * 用例描述：用户新建日程，数据验证失败
     */
    public function testStoreInvalid()
    {
        $postData['remark'] = 'route not found';

        // 测试路线不存在
        $resp = $this->callWantJson('post', 'route/1/daily', $postData);
        $this->assertJsonResponse($resp, 404);

        $testRoute = Route::firstOrFail();

        // 测试提交数据不存在
        $resp = $this->callWantJson('post', 'route/'. $testRoute['_id']. '/daily');
        $this->assertJsonResponse($resp, 400);
        $errorData = $resp->getData(true);
        $keys = ['error', 'data'];
        $this->arrayMustHasKeys($errorData, $keys);

        // 测试 sights 字段的验证
        $postData['remark'] = 'This is a test';
        $postData['sights'] = [];
        $resp = $this->callWantJson('post', 'route/'. $testRoute['_id']. '/daily');
        $this->assertJsonResponse($resp, 400);
        $errorData = $resp->getData(true);
        $keys = ['error', 'data'];
        $this->arrayMustHasKeys($errorData, $keys);

        // 测试非路线创建者新建日程
        $otherUser = \App\User::all()->toArray()[1];
        $this->be( \App\User::findOrFail($otherUser['_id']) );
        $resp = $this->callWantJson('post', 'route/'. $testRoute['_id']. '/daily', $postData);
        $this->assertJsonResponse($resp, 404);

    }

    /**
     * 测试方法：store
     * 用例描述：用户新建日程，新建成功
     */
    public function testStoreRight()
    {
        $postData['remark'] = 'This is a test';
        $testRoute = Route::firstOrFail();

        $resp = $this->callWantJson('post', 'route/'. $testRoute['_id'].'/daily', $postData);
        $this->assertJsonResponse($resp, 201);

        $respData = $resp->getData(true);
        $newRoute = Route::findOrFail($testRoute['_id'])->toArray();

        // 对比新建前后的记录数量
        $count['oldDaily'] = count($testRoute['daily']);
        $count['newDaily'] = count($newRoute['daily']);
        $this->assertEquals($count['oldDaily'] + 1, $count['newDaily']);

        // 测试数据字段
        $newDaily = last($newRoute['daily']);
        $keys = ['remark', '_id', 'sights', 'date'];
        $this->arrayMustHasKeys($respData,$keys);
        $keys = ['remark', '_id'];
        $this->arrayMustHasEqualKeyValues($newDaily, $respData, $keys);

        // 测试 sights 存在的情况
        $postData['sights'] = [
            [
                'sights_id' => Sight::getSightId('哈尔滨冰雪大世界'),
                'name' => '哈尔滨冰雪大世界',
                'loc' => ['type'=>'Point', 'coordinates' => [45.785779, 126.571317]],
            ],
            [
                'sights_id' => Sight::getSightId('儿童公园'),
                'name' => '儿童公园',
                'loc' => ['type'=>'Point', 'coordinates' => [45.767474, 126.662979]],
            ],
            [
                'sights_id' => Sight::getSightId('自由空间连锁宾馆大成店'),
                'name' => '自由空间连锁宾馆大成店',
                'loc' => ['type'=>'Point', 'coordinates' => [45.766688, 126.67072]],
            ]
        ];
        $testRoute = Route::firstOrFail();
        $resp = $this->callWantJson('post', 'route/'. $testRoute['_id'].'/daily', $postData);
        $this->assertJsonResponse($resp, 201);
        $respData = $resp->getData(true);
        $newRoute = Route::findOrFail($testRoute['_id'])->toArray();

        // 对比新建前后的记录数量
        $count['oldDaily'] = count($testRoute['daily']);
        $count['newDaily'] = count($newRoute['daily']);
        $this->assertEquals($count['oldDaily'] + 1, $count['newDaily']);
        $targetSightIds = array_fetch($postData['sights'], 'sights_id');

        // 新建的路线id应与这3个景点作关联
        $count['test'] = Sight::whereIn('_id', $targetSightIds)->where('routes', $testRoute['_id'])->count();
        $this->assertEquals(3, $count['test']);

        // 测试数据字段
        $newDaily = last($newRoute['daily']);
        $keys = ['remark', '_id', 'sights', 'date'];
        $this->arrayMustHasKeys($respData,$keys);
        $keys = ['remark', '_id'];
        $this->arrayMustHasEqualKeyValues($newDaily, $respData, $keys);
    }

    /**
     * 测试方法：update
     * 用例描述：用户更新日程信息，数据验证失败
     */
    public function testUpdateInvalid()
    {
        $testRoute = Route::firstOrFail();

        // 测试日程不存在
        $resp = $this->callWantJson('put', 'route/'. $testRoute['_id']. '/daily/9999');
        $this->assertJsonResponse($resp, 404);
        $errorData = $resp->getData(true);
        $this->assertArrayHasKey('error', $errorData);

        // 测试路线不存在
        $dailyId = head($testRoute['daily'])['_id'];
        $resp = $this->callWantJson('put', 'route/9999'. '/daily/'. $dailyId);
        $this->assertJsonResponse($resp, 404);
        $errorData = $resp->getData(true);
        $this->assertArrayHasKey('error', $errorData);

        // 测试景点列表不是数组的情况
        $putData['sights'] = 'not an array';
        $resp = $this->callWantJson('put', 'route/'. $testRoute['_id']. '/daily/'. $dailyId, $putData);
        $this->assertJsonResponse($resp, 400);
        $keys = ['error', 'data'];
        $errorData = $resp->getData(true);
        $this->arrayMustHasKeys($errorData, $keys);

        // 测试非路线创建者更新日程
        $otherUser = \App\User::all()->toArray()[1];
        $this->be( \App\User::findOrFail($otherUser['_id']) );
        $resp = $this->callWantJson('put', 'route/'. $testRoute['_id']. '/daily/'. $dailyId, $putData);
        $this->assertJsonResponse($resp, 404);
    }

    /**
     * 测试方法：update
     * 用例描述：用户更新日程信息，更新成功
     */
    public function testUpdateRight()
    {
        $putData['remark'] = 'This is a test';

        // 景点测试数据中，前两个是已存在景点，后面的均是新增的 
        $putData['sights'] = [
            [
                'sights_id' => Sight::getSightId('百度大厦'),
                'name' => '百度大厦',
                'loc' =>  ['type'=>'Point', 'coordinates' => [40.056968, 116.307689]],
            ],
            [
                'sights_id' => Sight::getSightId('圆明园遗址公园'),
                'name' => '圆明园遗址公园',
                'loc' => ['type'=>'Point', 'coordinates' => [40.01629, 116.314607]],
            ],
            [
                'sights_id' => \App\Sight::getSightId('哈尔滨冰雪大世界'),
                'name' => '哈尔滨冰雪大世界',
                'loc' => ['type'=>'Point', 'coordinates' => [45.785779, 126.571317]],
            ],
            [
                'sights_id' => \App\Sight::getSightId('儿童公园'),
                'name' => '儿童公园',
                'loc' => ['type'=>'Point', 'coordinates' => [45.767474, 126.662979]],
            ],
            [
                // 此处不设置 sights_id 用于测试可允许该字段不存在
                'name' => '自由空间连锁宾馆大成店',
                'loc' => ['type'=>'Point', 'coordinates' => [45.766688, 126.67072]],
            ],
            [
                'sights_id' => Sight::getSightId('欧亚酒店'),
                'name' => '欧亚酒店',
                'loc' =>  ['type'=>'Point', 'coordinates' => [23.151902, 113.312352]],
            ]
        ];
        $testRoute = Route::firstOrFail();
        $dailyId = head($testRoute['daily'])['_id'];

        $resp = $this->callWantJson('put', 'route/'. $testRoute['_id']. '/daily/'. $dailyId, $putData);
        $this->assertJsonResponse($resp);

        $respData = $resp->getData(true);
        $newRoute = Route::findOrFail($testRoute['_id']);
        $newDaily = head($newRoute['daily']);

        // 测试日程的基本字段
        $this->assertEquals($newDaily['_id'], $respData['_id']);
        $this->assertEquals($newDaily['remark'], $respData['remark']);

        // 测试景点数据
        $respSights = head($respData['sights']);
        $newSights = head($newDaily['sights']);
        $keys = ['sights_id', 'name', 'loc'];
        $this->arrayMustHasKeys($respSights, $keys);
        $this->arrayMustHasEqualKeyValues($newSights, $respSights, $keys);

        // 测试是否进行了景点与路线的关联
        $targetSightIds = array_fetch($putData['sights'], 'sights_id');
        $count = Sight::whereIn('_id', $targetSightIds)->where('routes', $testRoute['_id'])->count();
        $this->assertEquals(count($targetSightIds), $count);
    }

    /**
     * 测试方法：destroy
     * 用例描述：用户删除某一日程数据，删除失败
     */
    public function testDestroyInvalid()
    {
        // 测试路线不存在
        $resp = $this->callWantJson('delete', 'route/999/daily/999');
        $this->assertJsonResponse($resp, 404);
        $this->assertArrayHasKey('error', $resp->getData(true));

        // 测试日程不存在
        $testRoute = Route::firstOrFail();
        $resp = $this->callWantJson('delete', 'route/'. $testRoute['_id']. '/daily/999');
        $this->assertJsonResponse($resp, 404);
        $this->assertArrayHasKey('error', $resp->getData(true));

        // 测试非路线创建者删除日程
        $dailyId = head($testRoute['daily'])['_id'];
        $otherUser = \App\User::all()->toArray()[1];
        $this->be( \App\User::findOrFail($otherUser['_id']) );
        $resp = $this->callWantJson('delete', 'route/'. $testRoute['_id']. '/daily/'. $dailyId);
        $this->assertJsonResponse($resp, 404);
        $this->assertArrayHasKey('error', $resp->getData(true));
    }

    /**
     * 测试方法：destroy
     * 用例描述：用户删除某一日程数据，删除成功
     */
    public function testDestroyRight()
    {
        $testRoute = Route::firstOrFail();
        $dailyId = head($testRoute['daily'])['_id'];

        $this->assertNotEquals(0, Sight::where('routes', $testRoute['_id'])->count());
        $resp = $this->callWantJson('delete', 'route/'. $testRoute['_id']. '/daily/'. $dailyId);
        $this->assertJsonResponse($resp);
        $newRoute = Route::findOrFail($testRoute['_id']);

        // 对比删除前后的记录数量
        $count['old'] = count($testRoute['daily']);
        $count['new'] = count($newRoute['daily']);
        $this->assertEquals($count['new'] + 1, $count['old']);

        // 按当前数据填充，该日程的所有景点仅在该路线中出现一次，因而需要解除与该路线的关联
        $this->assertEquals(0, Sight::where('routes', $testRoute['_id'])->count());
    }
}