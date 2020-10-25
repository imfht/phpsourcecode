<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-4-23
 * Time: 下午11:46
 */

class CollectionControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->testUser = $this->getTestUser(true);
        $this->seed('CollectionTableTestSeeder');

        $this->testController = $this->app->make('App\Http\Controllers\CollectionController');
    }

    /**
     * 测试方法：　index
     */
    public function testIndex()
    {
        $resp = $this->testController->index();
        $respArray = $resp->getData(true);

        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertTrue( is_array($respArray) );

        $headItem = head($respArray);
        $this->assertEquals($this->testUser['_id'], $headItem['creator_id']);
        $keys = ['province', 'city', 'zone', 'longitude', 'latitude', 'address', 'label'];
        $this->arrayMustHasKeys($headItem, $keys, true);
    }

    /**
     *　测试方法：　store
     */
    public function testStore()
    {
        $posData['longitude'] = 22;
        $posData['latitude'] = 112;
        $posData['name'] = '佛山';
        $posData['label'] = '我家';
        $posData['address'] = '地址';

        $resp = $this->action('post', 'CollectionController@store',  $posData);
        $this->assertEquals(201, $resp->getStatusCode());

        $respDataArray = $resp->getData(true);
        $newId = $respDataArray['_id'];
        $newModel = \App\Collection::find($newId);
        $this->arrayMustHasEqualKeyValues(\App\Collection::changeDataToResp($newModel), $posData, array_keys($posData));

        //缺少经度
        unset($posData['longitude']);
        $this->action('post', 'CollectionController@store',  $posData);
        $this->assertResponseStatus(400);

        //缺少维度
        $posData['longitude'] = 22;
        unset($posData['latitude']);
        $this->action('post', 'CollectionController@store',  $posData);
        $this->assertResponseStatus(400);

        //缺乏命名
        $posData['latitude'] = 112;
        unset($posData['name']);
        $this->action('post', 'CollectionController@store',  $posData);
        $this->assertResponseStatus(400);

        //缺乏地址
        $posData['name'] = '佛山';
        unset($posData['address']);
        $this->action('post', 'CollectionController@store',  $posData);
        $this->assertResponseStatus(400);
    }

    /**
     * 测试方法：　true
     */
    public function testShow()
    {
        $testCollection = \App\Collection::all()->first();

        $resp = $this->testController->show($testCollection['_id']);
        $this->assertEquals(200, $resp->getStatusCode());

        $respDaraArray = $resp->getData(true);
        $keys = ['province', 'city', 'zone', 'longitude', 'latitude', 'address', 'label'];
        $this->arrayMustHasEqualKeyValues(\App\Collection::changeDataToResp($testCollection), $respDaraArray, $keys);

        //对应查找不到
        $resp = $this->testController->show(404);
        $this->assertEquals(404, $resp->getStatusCode());
    }

    /**
     * 测试方法: destroy
     */
    public function testDestroy()
    {
        $testCollection = \App\Collection::all()->first();

        $resp = $this->testController->destroy($testCollection['_id']);
        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEmpty( \App\Collection::find($testCollection['_id']) );


        //对应查找不到
        $resp = $this->testController->destroy(404);
        $this->assertEquals(404, $resp->getStatusCode());
    }

    private $testUser;

    private $testController;
}