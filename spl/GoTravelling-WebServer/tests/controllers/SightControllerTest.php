<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-5-5
 * Time: 下午4:12
 */
use App\Sight;

class SightControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');

        $this->seedDB();
    }

    /**
     * 测试方法： store
     * 用例描述： 应该是成功存储的
     */
    public function testStoreRight()
    {
        $this->getTestUser(true);

        $postData = [
            'province' => '广东',
            'city' => '深圳',
            'loc' => ['type'=>'Point', 'coordinates' => [22.507935, 113.905]],
            'name' => '月亮湾公园',
            'description' => '看月亮啦',
            'address' => '深圳市南山区前海路0333号阳光玫瑰园对面',
        ];

        $resp = $this->callWantJson('post', 'api/sight', $postData);
        $this->assertJsonResponse($resp, 201);

        $respData = $resp->getData(true);
        $added = Sight::findOrFail($respData['_id']);
        $this->arrayMustHasEqualKeyValues($added->toArray(), $postData, ['province']);
    }

    /**
     * 测试方法：store
     * 用例描述：由于缺乏必须的字段，因此应该是失败的
     */
    public function testStoreWrongNotRequired()
    {
        $this->getTestUser(true);

        $postData = [
            'province' => '广东',
            'city' => '深圳',
            'loc' => ['type'=>'Point', 'coordinates' => [22.507935, 113.905]],
            'name' => '月亮湾公园',
            'description' => '看月亮啦',
            'address' => '深圳市南山区前海路0333号阳光玫瑰园对面',
        ];

        $requiredFields = [
          'province', 'city', 'loc', 'name', 'address'
        ];

        foreach($requiredFields as $field ){
            $this->tryCreateFailure($postData, $field);
        }
    }

    /**
     * 测试方法： store
     * 用例描述： 由于字段 loc 格式不正确，因此应该是失败的
     */
    public function testStoreInvalidLoc()
    {
        $this->getTestUser(true);

        $postData = [
            'province' => '广东',
            'city' => '深圳',
            'loc' => ['type'=>'Point', 'coordinates' => [22.507935, 113.905]],
            'name' => '月亮湾公园',
            'description' => '看月亮啦',
            'address' => '深圳市南山区前海路0333号阳光玫瑰园对面',
        ];
        $sourceLoc = $postData['loc'];

        $postData['loc']['type'] = 'Line';
        $this->tryCreateFailure($postData);

        $postData['loc'] = $sourceLoc;
        $postData['loc']['coordinates'][0] = 181;
        $this->tryCreateFailure($postData);

        $postData['loc'] = $sourceLoc;
        $postData['loc']['coordinates'][1] = 181;
        $this->tryCreateFailure($postData);

        $postData['loc'] = $sourceLoc;
        unset($postData['loc']['type']);
        $this->tryCreateFailure($postData);

        $postData['loc'] = $sourceLoc;
        unset($postData['loc']['coordinates']);
        $this->tryCreateFailure($postData);
    }

    /**
     * 辅助方法，该新建尝试应该是失败的.
     *
     * @param array $postData 待新建所传递的数据
     * @param null|string $unsetField 可选，如填入则 $postData 中相应字段被清空
     */
    protected function tryCreateFailure(array $postData, $unsetField = null)
    {
        if( $unsetField ){
            unset($postData[$unsetField]);
        }

        $resp = $this->callWantJson('post', 'api/sight', $postData);
        $this->assertJsonResponse($resp, 400);

        $respData = $resp->getData(true);
        $this->assertNotEmpty($respData['error']);
    }

    /**
     * 测试方法: show
     * 用例描述：操作应该是成功的
     */
    public function testShow()
    {
        $this->getTestUser();

        $testTarget = Sight::firstOrFail();
        $resp = $this->callWantJson('get', 'api/sight/'. $testTarget['_id']);
        $this->assertJsonResponse($resp);

        $respData = $resp->getData(true);

        $keys = ['name', 'description', 'address', 'province', 'city', 'loc'];
        $this->arrayMustHasEqualKeyValues($respData, $testTarget->toArray(), $keys);
    }

    /**
     * 测试方法： update
     * 用例描述：用户更新景点基本信息，操作失败
     */
    public function testUpdateBaseInvalid()
    {
        $this->getTestUser();

        $testSight = Sight::firstOrFail();

        $putData = [];
        $resp = $this->callWantJson('put', 'api/sight/'. $testSight['_id'], $putData);
        $this->assertJsonResponse($resp, 400);

        $putData['type'] = 'base';
        $resp = $this->callWantJson('put', 'api/sight/9999', $putData);
        $this->assertJsonResponse($resp, 404);

        $putData['province'] = '6ahd7da';
        $resp = $this->callWantJson('put', 'api/sight/'. $testSight['_id'], $putData);
        $this->assertJsonResponse($resp, 400);
        $keys = ['error', 'data'];
        $respData = $resp->getData(true);
        $this->arrayMustHasKeys($respData, $keys);

        unset($putData['province']);
        $putData['loc'] = ['type' => 'Box'];
        $resp = $this->callWantJson('put', 'api/sight/'. $testSight['_id'], $putData);
        $this->assertJsonResponse($resp, 400);
        $keys = ['error', 'data'];
        $respData = $resp->getData(true);
        $this->arrayMustHasKeys($respData, $keys);
    }

    /**
     * 测试方法： update
     * 用例描述：用户更新景点基本信息，操作成功
     */
    public function testUpdateBase()
    {
        $this->getTestUser();
        $putData['type'] = 'base';
        $putData['province'] = '广东';
        $putData['city'] = '广州';
        $putData['name'] = 'A wonderful world';
        $putData['description'] = 'what dose the fox said';
        $putData['address'] = 'This is not a address, fuck';
        $putData['loc'] = ['type'=>'Point', 'coordinates'=>[23.1852,78.9251]];

        $testSight = Sight::firstOrFail();
        $resp = $this->callWantJson('put', 'api/sight/'. $testSight['_id'], $putData);
        $this->assertJsonResponse($resp);
        $respData = $resp->getData(true);
        $keys = ['_id', 'name', 'province', 'city', 'description', 'address'];
        $newSight = Sight::findOrFail($testSight['_id'])->toArray();
        $this->arrayMustHasEqualKeyValues($newSight, $respData, $keys);
        $locKeys = ['type', 'coordinates'];
        $this->arrayMustHasEqualKeyValues($putData['loc'], $respData['loc'], $locKeys);
    }

    /**
     * 测试方法： update
     * 用例描述： 用户成功签到, 重复签到会失败
     */
    public function testUpdateCheckIn()
    {
        $testUser = $this->getTestUser();
        $testTarget = Sight::firstOrFail();

        $putData['type'] = 'check_in';
        $resp = $this->callWantJson('put', 'api/sight/'. $testTarget['_id'], $putData);
        $this->assertJsonResponse($resp);
        $this->assertEquals(Sight::where('_id', $testTarget['_id'])->where('check_in', $testUser['_id'])->count(), 1);
        $this->assertCount($testTarget['check_in_num'] + 1, Sight::where('_id', $testTarget['_id'])->firstOrFail()->toArray()['check_in']);

        //重复签到
        $resp = $this->callWantJson('put', 'api/sight/'. $testTarget['_id'], $putData);
        $this->assertJsonResponse($resp, 403);
    }

    /**
     * 测试方法：update
     * 用例描述：用户上传图片，操作是失败的
     */
    public function testUpdateImageInvalid()
    {
        $this->getTestUser();
        $testSight = Sight::firstOrFail();
        $putData['type'] = 'images';
        $path = public_path(). '/image/test/';

        // 不合法的图片格式
        $images = [
            $path. 'iconImage1.ico',
            $path. 'iconImage2.ico'
        ];
        $uploadFiles['images'] = $this->getTestImageList($images, false);
        $resp = $this->callWantJson('put', 'api/sight/'. $testSight['_id'], $putData, [], $uploadFiles);
        $this->assertJsonResponse($resp, 400);
        $keys = ['data', 'error'];
        $this->arrayMustHasKeys($resp->getData(true), $keys, true);

        // 图片文件过大
        $images = [ $path. 'bigImage.jpg' ];
        $uploadFiles['images'] = $this->getTestImageList($images, false);
        $resp = $this->callWantJson('put', 'api/sight/'. $testSight['_id'], $putData, [], $uploadFiles);
        $this->assertJsonResponse($resp, 400);
        $keys = ['data', 'error'];
        $this->arrayMustHasKeys($resp->getData(true), $keys, true);
    }

    /**
     * 测试方法：update
     * 用例描述：用户通过文件上传的方式上传图片，操作是成功的
     */
    public function testUpdateFileRight()
    {
        $this->getTestUser();
        // 生成测试用的图片文件列表
        $path = public_path(). '/image/test/';
        $images = [
            $path. 'image1.png',
            $path. 'image2.png'
        ];
        $uploadFiles['images'] = $this->getTestImageList($images, true);
        $putData['type'] = 'images';

        $testSight = Sight::firstOrFail();
        $count['old'] = count($testSight['images']);
        $resp = $this->callWantJson('put', 'api/sight/'. $testSight['_id'], $putData, [], $uploadFiles);
        $this->assertJsonResponse($resp);

        // 检查是否新建了图片数据
        $newSight = Sight::findOrFail($testSight['_id']);
        $count['new'] = count($newSight['images']);
        $this->assertEquals($count['old'] + 2, $count['new']);

        // 删除测试生成的测试用图片文件
        $path = public_path(). '/image/sight/';
        foreach ($newSight['images'] as $image) {
            unlink($path. $image);
        }
    }

    /**
     * 测试方法：update
     * 用例描述：用户通过base64编码的方式上传图片，操作是成功的
     */
    public function testUpdateBase64Right()
    {
        $this->getTestUser();
        $putData['type'] = 'images';
        $putData['images']= [];

        // 生成测试用的图片文件列表
        $path = public_path(). '/image/test/';
        $files = [
            $path. 'image1.png',
            $path. 'image2.png'
        ];
        foreach ($files as $file) {
            $fileData = file_get_contents($file);
            $tempData = base64_encode($fileData);
            array_push($putData['images'], $tempData);
        }

        $testSight = Sight::firstOrFail();
        $count['old'] = count($testSight['images']);
        $resp = $this->callWantJson('put', 'api/sight/'. $testSight['_id'], $putData);
        $this->assertJsonResponse($resp);

        // 检查是否新建了图片数据
        $newSight = Sight::findOrFail($testSight['_id']);
        $count['new'] = count($newSight['images']);
        $this->assertEquals($count['old'] + 2, $count['new']);

        // 删除测试生成的测试用图片文件
        $path = public_path(). '/image/sight/';
        foreach ($newSight['images'] as $image) {
            unlink($path. $image);
        }
    }


    /**
     * 填充数据库
     */
    public function seedDB()
    {
        $this->seed('UserTableTestSeeder');
        $this->seed('SightTableTestSeeder');
    }

}