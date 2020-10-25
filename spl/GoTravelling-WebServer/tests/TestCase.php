<?php

use App\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	/**
	 * Creates the application.
	 *
	 * @return \Illuminate\Foundation\Application
	 */
	public function createApplication()
	{
		$app = require __DIR__.'/../bootstrap/app.php';

		$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

		return $app;
	}

    /**
     * 对 Laravel Framework 中原有 seed 方法进行封装，
     * 目的在于，如果曾经进行了封装，则在这个类测试接受对数据库进行相关的清理操作.
     *
     * @param string $seeder
     */
    public function seed($seeder)
    {
        if( is_null(static::$mongoClient) ){
            static::$mongoClient = DB::getMongoDB();
        }

        parent::seed($seeder);
    }

    /**
     * 对框架内的assertArrayHasKey进行扩展，用于测试数组是否具备多个键值.
     *
     * @param mixed $dataArray  待测试的数组
     * @param array $keys 待测试的键值
     * @param bool $defined 是否测试该键值不为null，默认为不测试
     */
    public function arrayMustHasKeys($dataArray, array $keys, $defined = false)
    {
        foreach($keys as $currentKey){
            $this->assertArrayHasKey($currentKey, $dataArray);
            if( $defined ){
                $this->assertNotNull( $dataArray[ $currentKey ] );
            }
        }
    }

    /**
     * 对内置assertEquals的封装，测试两个数组含有值相同的键值对.
     *
     * @param array $arrayL
     * @param array $arrayR
     * @param array $keys
     */
    public function arrayMustHasEqualKeyValues(array $arrayL, array $arrayR, array $keys)
    {
        foreach ($keys as $currentKey) {
            $this->assertEquals($arrayL[$currentKey], $arrayR[$currentKey]);
        }
    }

    /**
     * 随机登陆一个用户，并返回该用户所对应的User模型实例.
     *
     * @param bool $seedDB 是否进行数据库填从，默认为否
     * @param bool $login 是否进行用户登陆，默认为登陆
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    protected function getTestUser($seedDB = false, $login = true)
    {
        if( $seedDB ){
            $this->seed('UserTableTestSeeder');
        }

        $testUser = User::firstOrFail();
        if( $login ){
            $this->be($testUser);
        }

        return $testUser;
    }

    /**
     * 基于对原 seed　方法的修改，当进行了数据库填充后，在这个类测试结束后进行相关的清理操作.
     */
    public function tearDown()
    {
        if( ! is_null(static::$mongoClient) ){
            $this->dropTestingDatabase();
            static::$mongoClient = null;
        }

        parent::tearDown();
    }

    /**
     * 清空测试用的数据库数据
     */
    public function dropTestingDatabase()
    {
        DB::getMongoDB()->drop();
    }

    /**
     * 对原方法 $this->call 的基本封装，加入请求头部 'HTTP_ACCEPT': 'application/json'.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  array   $parameters
     * @param  array   $cookies
     * @param  array   $files
     * @param  array   $server
     * @param  string  $content
     * @return \Illuminate\Http\Response
     */
    public function callWantJson($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $server = array_merge($server, [ 'HTTP_ACCEPT' => 'application/json' ]);

        return $this->call($method, $uri, $parameters, $cookies, $files, $server, $content);
    }

    /**
     * 此方法用于获取测试用的图片列表
     *
     * @param array $fileFullPaths 图片文件的绝对路径，支持多个图片文件
     * @param bool $copy 设置是否复制一份临时副本来替代原文件，默认是true，表示复制
     * @param string $type 设置临时副本的后缀名，默认是 'png'，就当前设置来说，暂无用处
     * @param string $prefix 设置临时副本的前缀名称
     * @return array 图片文件列表，每个元素均为 UploadedFile 对象
     */
    public function getTestImageList(array $fileFullPaths, $copy = true, $type = 'png', $prefix = 'test')
    {
        $testImageList = [];
        if ( true === $copy ) {
            // 使用临时副本生成图片列表
            $count = 1;
            foreach ($fileFullPaths as $file) {
                $tempFileName = public_path() . '/image/test/' . $prefix . $count . '.' . $type;
                copy($file, $tempFileName);
                $tempFile = $this->getTestImage($tempFileName, $tempFileName);
                array_push($testImageList, $tempFile);
                $count += 1;
            }
        } else {
            // 使用原图片文件生成图片列表
            foreach ($fileFullPaths as $file) {
                $tempFile = $this->getTestImage($file, last( explode('/', $file) ));
                array_push($testImageList, $tempFile);
            }
        }

        return $testImageList;
    }


    /**
     * 此方法简单地封装了实例化 UploadedFile 对象的逻辑，使之适用与测试
     *
     * @param string $path 文件的绝对路径
     * @param string $originalName 文件的原始名称
     * @param null $mimeType 文件的MIME类型
     * @param null $size 文件的大小
     * @param null $error 上传文件的错误消息
     * @return UploadedFile
     */
    public function getTestImage($path, $originalName, $mimeType = null, $size = null, $error = null)
    {
        return new UploadedFile($path, $originalName, $mimeType, $size, $error, true); // 最后一个参数必须是true，开启测试模式
    }

    /**
     * 断言该响应的头部信息是否为 Json 格式以及返回的状态码
     *
     * @param $response
     * @param int $code 状态码，默认为200
     */
    public function assertJsonResponse($response, $code = 200)
    {
        $this->assertTrue( str_contains($response->headers->get('content-type'), 'application/json') );
        $this->assertEquals($code, $response->getStatusCode());
    }
    /*
     * 保留　MongoClient　的实例
     */
    protected static $mongoClient = null;
}
