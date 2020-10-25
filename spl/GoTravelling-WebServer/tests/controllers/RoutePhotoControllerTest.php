<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-5-17
 * Time: 下午8:18
 */
class RoutePhotoControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');
        $this->seedDB();
        $this->getTestUser();
    }

    /**
     * 测试方法： store
     * 用例描述：用户添加旅行照片，操作失败
     */
    public function testStoreInvalid()
    {
        // 测试路线不存在
        $resp = $this->callWantJson('post', 'api/route/999/photo');
        $this->assertJsonResponse($resp, 404);
        $this->assertArrayHasKey('error', $resp->getData(true));

        // 测试没有提交数据的情况
        $testRoute = \App\Route::firstOrFail();
        $resp = $this->callWantJson('post', 'api/route/'. $testRoute['_id']. '/photo');
        $this->assertJsonResponse($resp, 400);
        $keys = ['error', 'data'];
        $this->arrayMustHasKeys($resp->getData(true), $keys);

        $fileParam['path'] = public_path(). '/image/logo.png';
        $fileParam['originalName'] = 'logo.png';
        $fileParam['mime'] = 'image/png';
        $fileParam['size'] = 1024;

        // 测试图片格式
        $invalidParam = $fileParam;
        $invalidParam['path'] = public_path(). '/image/min-logo.ico';
        $uploadFile['photo'] = $this->getTestImage($invalidParam['path'], $invalidParam['originalName'], $invalidParam['mime'], $invalidParam['size']);
        $resp = $this->callWantJson('post', 'api/route/'. $testRoute['_id']. '/photo', [], [], $uploadFile);
        $this->assertJsonResponse($resp, 400);
        $keys = ['error', 'data'];
        $this->arrayMustHasKeys($resp->getData(true), $keys);

        // 测试图片大小
        $invalidParam = $fileParam;
        $invalidParam['path'] = public_path(). '/image/test.jpg';
        $uploadFile['photo'] = $this->getTestImage($invalidParam['path'], $invalidParam['originalName'], $invalidParam['mime'], $invalidParam['size']);
        $resp = $this->callWantJson('post', 'api/route/'. $testRoute['_id']. '/photo', [], [], $uploadFile);
        $this->assertJsonResponse($resp, 400);
        $keys = ['error', 'data'];
        $this->arrayMustHasKeys($resp->getData(true), $keys);

        // 备注：base64的无效情况待测试
    }

    /**
     * 测试方法： store
     * 用例描述：用户通过图片文件上传的方式添加旅行图片，操作成功
     */
    public function testStoreFileRight()
    {
        $testRoute = \App\Route::firstOrFail();
        $fileParam['path'] = public_path(). '/image/';
        $fileParam['fileName'] = 'logo.png';
        // 按当前测试设定，以下字段无实质作用
        $fileParam['originalName'] = 'logo.png';
        $fileParam['mime'] = 'image/png';
        $fileParam['size'] = 1024;

        // 复制图片文件，用于测试
        $tempFile = 'test.png';
        copy($fileParam['path']. $fileParam['fileName'], $fileParam['path']. $tempFile);
        $fileParam['fileName'] = $tempFile;

        $uploadFile['photo'] = $this->getTestImage(
            $fileParam['path']. $fileParam['fileName'],
            $fileParam['originalName'], $fileParam['mime'], $fileParam['size']
        );
        $resp = $this->callWantJson('post', 'api/route/'. $testRoute['_id']. '/photo', [], [], $uploadFile);
        $this->assertJsonResponse($resp, 201);
        $respData = $resp->getData(true);

        //
        $keys = ['_id', 'name'];
        $this->arrayMustHasKeys($respData,$keys, true);

        // 删除测试生成的图片文件
        unlink(public_path(). '/image/routePhoto/'. $respData['name']);
    }

    /**
     * 测试方法： store
     * 用例描述：用户添加旅行图片，图片文件采用base64编码，操作成功
     */
    public function testStoreBase64Right()
    {
        $testRoute = \App\Route::firstOrFail();
        $path = public_path(). '/image/';
        $fileName = 'logo.png';

        // 对图片进行base64编码
        $photo = file_get_contents($path. $fileName);
        $postData['photo'] = base64_encode($photo);
        $resp = $this->callWantJson('post', 'api/route/'. $testRoute['_id']. '/photo', $postData);
        $this->assertJsonResponse($resp, 201);
        $respData = $resp->getData(true);

        //
        $keys = ['_id', 'name'];
        $this->arrayMustHasKeys($respData,$keys, true);

        // 删除测试生成的图片文件
        unlink(public_path(). '/image/routePhoto/'. $respData['name']);
    }

    /**
     * 测试方法：destroy
     * 用例描述：用户删除旅行照片，操作失败
     */
    public function testDestroyInvalid()
    {
        // 测试照片不存在的情况
        $testRoute = \App\Route::firstOrFail();
        $resp = $this->callWantJson('delete', 'api/route/'. $testRoute['_id']. '/photo/999');
        $this->assertJsonResponse($resp, 404);
        $this->assertArrayHasKey('error', $resp->getData(true));
    }

    /**
     * 测试方法：destroy
     * 用例描述：用户删除旅行图片，操作成功
     */
    public function testDestroyRight()
    {
        $testRoute = \App\Route::firstOrFail();
        $photoId = head($testRoute['photo'])['_id'];
        $count['old'] = count($testRoute['photo']);
        $resp = $this->callWantJson('delete', 'api/route/'. $testRoute['_id']. '/photo/'. $photoId);
        $this->assertJsonResponse($resp);
        $count['new'] = count(\App\Route::findOrFail($testRoute['_id'])['photo']);

        // 对比删除前后的记录条数
        $this->assertEquals($count['new'] + 1, $count['old']);
        
        // 再查找已被删除的图片记录
        $count['test'] = \App\Route::where('_id', $testRoute['_id'])
            ->where('photo._id', intval($photoId))->count();
        $this->assertEquals(0, $count['test']);
    }

    protected function seedDB()
    {
        $this->seed('UserTableTestSeeder');
        $this->seed('SightTableTestSeeder');
        $this->seed('RoutesTableTestSeeder');
    }
}