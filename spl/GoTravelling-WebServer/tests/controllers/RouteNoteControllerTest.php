<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-5-29
 * Time: 下午10:14
 */
class RouteNoteControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');

        $this->seedDB();
        $this->getTestUser();
    }

    /**
     * 测试方法：index
     * 用例描述：应该返回该路线的所有小记信息
     */
    public function testIndexRight()
    {
        $testRoute = \App\Route::firstOrFail();

        $resp = $this->callWantJson('get', 'api/route/'. $testRoute['_id']. '/note');
        $this->assertJsonResponse($resp);

        // 测试返回数据
        $respData = $resp->getData(true);
        $testNotes = \App\RouteNote::where('route_id', $testRoute['_id'])->get()->toArray();
        $keys = ['_id', 'route_id', 'content', 'created_at'];
        $this->arrayMustHasEqualKeyValues(head($testNotes), head($respData), $keys); // 根据当前数据填充，该路线至少存在一条小记数据

        // 测试返回字段
        $keys = array_merge($keys, ['loc_name', 'loc', 'images']);
        $this->arrayMustHasKeys(head($respData), $keys, true);
    }

    /**
     * 测试方法：show
     * 用例描述：用户查询某一路线的某条小记，操作是失败的
     */
    public function testShowInvalid()
    {
        $testRoute = \App\Route::firstOrFail();

        // 测试小记id不存在的情况
        $resp = $this->callWantJson('get', 'api/route/'. $testRoute['_id']. '/note/999');
        $this->assertJsonResponse($resp,404);
        $this->assertArrayHasKey('error', $resp->getData(true));

        $testNotes = \App\RouteNote::where('route_id', $testRoute['_id'])->get()->toArray();
        $noteId = head($testNotes)['_id'];

        // 测试路线不存在的情况
        $resp = $this->callWantJson('get', 'api/route/9999/note/'. $noteId);
        $this->assertJsonResponse($resp,404);
        $this->assertArrayHasKey('error', $resp->getData(true));
    }

    /**
     * 测试方法：show
     * 用例描述：用户查看某一路线的某条小记信息，操作成功
     */
    public function testShowRight()
    {
        $testRoute = \App\Route::firstOrFail();
        $testNotes = \App\RouteNote::where('route_id', $testRoute['_id'])->get()->toArray();
        $noteId = head($testNotes)['_id']; // 根据当前数据填充，该路线至少存在一条小记数据

        $resp = $this->callWantJson('get', 'api/route/'. $testRoute['_id']. '/note/'. $noteId);
        $this->assertJsonResponse($resp);

        // 测试返回数据
        $respData = $resp->getData(true);
        $targetNote = head($testNotes);
        $keys = ['_id', 'route_id', 'content', 'created_at'];
        $this->arrayMustHasEqualKeyValues($targetNote, $respData, $keys);

        // 测试返回字段
        $keys = array_merge($keys, ['loc_name', 'loc', 'images']);
        $this->arrayMustHasKeys($respData, $keys, true);
    }

    /**
     * 测试方法：store
     * 用例描述：用户新建路线小记，操作是失败的
     */
    public function testStoreInvalid()
    {
        // 测试路线不存在的情况
        $resp = $this->callWantJson('post', 'api/route/999/note');
        $this->assertJsonResponse($resp, 404);
        $this->assertArrayHasKey('error', $resp->getData(true));

        // 测试缺少必须的提交数据字段
        $this->tryStoreInvalid([]);

        // 测试错误的 location 字段格式
        $postData['content'] = '测试的小记内容';
        $postData['loc_name'] = '错误的location格式';
        $this->tryStoreInvalid($postData);

        //
        $postData['loc'] = '';
        $this->tryStoreInvalid($postData);

        // 测试图片格式错误
        $postData['loc'] = [
            'type' => 'Point',
            'coordinates' => [23.134, 110.3842]
        ];
        $path = public_path(). '/image/test/';
        $files = [
            $path. 'iconImage1.ico',
            $path. 'iconImage2.ico'
        ];
        $uploadFile['images'] = $this->getTestImageList($files, false);
        $this->tryStoreInvalid($postData,$uploadFile);

        // 测试图片过大
        $files = [ $path. 'bigImage.jpg'];
        $uploadFile['images'] = $this->getTestImageList($files, false);
        $this->tryStoreInvalid($postData, $uploadFile);
    }

    /**
     * 辅助方法，用户尝试新建路线小记，操作是失败的
     * @param array $postData 新建的小记数据
     * @param array $uploadFile 附带的图片数据
     */
    protected function tryStoreInvalid($postData, $uploadFile = array())
    {
        $testRoute = \App\Route::firstOrFail();
        $resp = $this->callWantJson('post', 'api/route/'. $testRoute['_id']. '/note', $postData, [], $uploadFile);
        $this->assertJsonResponse($resp, 400);
        $keys = ['error', 'data'];
        $this->arrayMustHasKeys($resp->getData(true), $keys, true);
    }

    /**
     * 测试方法：store
     * 用例描述：用户新建路线小记，图片是通过文件上传的方式提交的，操作成功
     */
    public function testStoreFileRight()
    {
        $testRoute = \App\Route::firstOrFail();

        $postData['content'] = '测试的小记内容';
        $postData['loc_name'] = '西三宿舍楼';
        $testLoc = [
            'type' => 'Point',
            'coordinates' => [23.5732, 100.57264]
        ];
        //
        $postData['loc'] = json_encode($testLoc);

        // 生成测试用的图片文件列表
        $path = public_path(). '/image/test/';
        $files = [
            $path. 'image1.png',
            $path. 'image2.png'
        ];
        $uploadFile['images'] = $this->getTestImageList($files);

        $resp = $this->callWantJson('post', 'api/route/'. $testRoute['_id']. '/note', $postData, [], $uploadFile);
        $this->assertJsonResponse($resp, 201);

        // 测试返回字段和数据
        $respData = $resp->getData(true);
        $keys = ['content', 'loc_name'];
        $this->arrayMustHasEqualKeyValues($postData, $respData, $keys);
        $keys = array_merge($keys, ['created_at', 'images']);
        $this->arrayMustHasKeys($respData, $keys, true);

        // 测试新建后，图片的张数是否正确
        $this->assertEquals(count($files), count($respData['images']));

        // 删除测试用临时图片文件
        foreach ($respData['images'] as $image) {
            unlink(public_path(). '/image/routeNote/'. $image);
        }
    }

    /**
     * 测试方法：store
     * 用例描述：用户新建路线小记，图片是通过base64编码的方式提交的，操作成功
     */
    public function testStoreBase64Right()
    {
        $testRoute = \App\Route::firstOrFail();

        $postData['content'] = '测试的小记内容';
        $postData['loc_name'] = '西三宿舍楼';
        $postData['loc'] = [
            'type' => 'Point',
            'coordinates' => [23.5732, 100.57264]
        ];
        $postData['images'] = [];

        // 生成测试用的图片文件列表
        $path = public_path(). '/image/test/';
        $files = [
            $path. 'image1.png',
            $path. 'image2.png'
        ];
        foreach ($files as $file) {
            $fileData = file_get_contents($file);
            $tempData = base64_encode($fileData);
            array_push($postData['images'], $tempData);
        }

        $resp = $this->callWantJson('post', 'api/route/'. $testRoute['_id']. '/note', $postData);
        $this->assertJsonResponse($resp, 201);

        // 测试返回字段和数据
        $respData = $resp->getData(true);
        $keys = ['content', 'loc_name'];
        $this->arrayMustHasEqualKeyValues($postData, $respData, $keys);
        $keys = array_merge($keys, ['created_at', 'images']);
        $this->arrayMustHasKeys($respData, $keys, true);

        // 测试新建后，图片的张数是否正确
        $this->assertEquals(count($files), count($respData['images']));

        // 删除测试用临时图片文件
        foreach ($respData['images'] as $image) {
            unlink(public_path(). '/image/routeNote/'. $image);
        }
    }

    /**
     * 测试方法：destroy
     * 用例描述：用户删除路线小记，操作是失败的
     */
    public function testDestroyInvalid()
    {
        $testRoute = \App\Route::firstOrFail();

        // 测试小记不存在的情况
        $resp = $this->callWantJson('delete', 'api/route/'. $testRoute['_id']. '/note/999');
        $this->assertJsonResponse($resp, 404);
        $this->assertArrayHasKey('error', $resp->getData(true));

        $testNote = \App\RouteNote::where('route_id', $testRoute['_id'])->firstOrFail();
        $noteId = $testNote['_id'];

        // 测试路线不存在的情况
        $resp = $this->callWantJson('delete', 'api/route/999/note/'. $noteId);
        $this->assertJsonResponse($resp, 404);
        $this->assertArrayHasKey('error', $resp->getData(true));

        // 测试非创建者删除路线小记
        $otherUser = \App\User::all()->toArray()[1];
        $this->be( \App\User::findOrFail($otherUser['_id']) );
        $testRoute = \App\Route::firstOrFail();
        $resp = $this->callWantJson('delete', 'api/route/'. $testRoute['_id']. '/note/'. $noteId);
        $this->assertJsonResponse($resp, 404);
        $this->assertArrayHasKey('error', $resp->getData(true));
    }

    /**
     * 测试方法：destroy
     * 用例描述：用户删除路线小记数据，操作是成功的
     */
    public function testDestroyRight()
    {
        $testRoute = \App\Route::firstOrFail();
        $testNote = \App\RouteNote::where('route_id', $testRoute['_id'])->firstOrFail();
        $noteId = $testNote['_id'];

        $resp = $this->callWantJson('delete', 'api/route/'. $testRoute['_id']. '/note/'. $noteId);
        $this->assertJsonResponse($resp);

        // 再次查询该小记数据，结果应该为空
        $this->assertTrue( is_null(\App\RouteNote::find($noteId)) );
    }

    protected function seedDB()
    {
        $this->seed('UserTableTestSeeder');
        $this->seed('SightTableTestSeeder');
        $this->seed('RoutesTableTestSeeder');
        $this->seed('RouteTagTableSeeder');
        $this->seed('RouteNotesTableTestSeeder');
    }
}