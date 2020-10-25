<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-11-12
 * Time: 上午12:17
 */

/**
 * Class UserControllerTest
 *
 * 此类用于测试UserController
 *
 */
class UserControllerTest extends TestCase
{
    /**
     * 执行一些初始化操作
     */
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->userControllerObj = $this->app->make('UserController');
    }

    /**
     * 测试UserController的getInfo方法，该方法应该成功返回结果.
     */
    public function testGetInfoRight()
    {
        //待测试的用户id
        $testUserId = 1;

        //填从测试所用的数据
        $this->seed('UserTableTestSeeder');

        $rep = $this->userControllerObj->getInfo($testUserId);

        $this->assertEquals(200, $rep->getStatusCode());
        $repDataArray = $rep->getData(true);

        $this->assertTrue(is_array($repDataArray));
        $keys = ['username', 'email', 'head_image', 'description'];
        $this->arrayMustHasKeys($repDataArray, $keys);

        $targetUser = User::find($testUserId)->toArray();
        $this->arrayMustHasEqualKeyValues($repDataArray, $targetUser,  $keys);
    }

    /**
     * 测试UserController的getInfo方法，该方法应该抛出异常
     */
    public function testGetInfoWrong()
    {
        $testUserId = 1;

        //在没有找到对应的用户时，应该抛出异常
        $this->setExpectedException('Illuminate\Database\Eloquent\ModelNotFoundException');
        $this->userControllerObj->getInfo($testUserId)->getData();
    }

    /**
     * 测试UserController的getExist方法
     */
    public function testGetExist()
    {

        $rep = $this->userControllerObj->getExist('testUserName');
        $this->assertEquals(404, $rep->getStatusCode());

        $rep = $this->userControllerObj->getExist('testUserEmail');
        $this->assertEquals(404, $rep->getStatusCode());

        //填从测试所用的数据
        $this->seed('UserTableTestSeeder');
        $existUser = User::first();

        $keys = ['id', 'username', 'head_image', 'description'];

        $rep = $this->userControllerObj->getExist( $existUser['username'] );
        $this->assertEquals(200, $rep->getStatusCode());
        $this->arrayMustHasEqualKeyValues($rep->getData(true), $existUser->toArray(), $keys);

        $rep = $this->userControllerObj->getExist( $existUser['email'] );
        $this->assertEquals(200, $rep->getStatusCode());
        $this->arrayMustHasEqualKeyValues($rep->getData(true), $existUser->toArray(), $keys);

    }

    private $userControllerObj; //引用UserController的实例
}