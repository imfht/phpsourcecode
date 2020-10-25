<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-4-28
 * Time: 下午7:24
 */
use App\User;

class UserControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');
    }

    /**
     * 测试对于存在的用户名或电话号码，是否正确提示已存在.
     */
    public function testExist()
    {
        $this->seed('UserTableTestSeeder');

        $testUser = User::first();
        $this->call('get', 'api/user/exist/'. $testUser['username']);
        $this->assertResponseOk();

        $this->call('get', 'api/user/exist/'. $testUser['cellphone_number']);
        $this->assertResponseOk();
    }

    /**
     * 测试对于不存在的用户名或电话号码，是否正确提示不存在.
     */
    public function testNotExist()
    {
        $testUser['username'] = 'invalid';
        $testUser['cellphone_number'] = 'invalid';

        $this->call('get', 'api/user/exist/'. $testUser['username']);
        $this->assertResponseStatus(404);
        $this->call('get', 'api/user/exist/'. $testUser['cellphone_number']);
        $this->assertResponseStatus(404);

    }
}