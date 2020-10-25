<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-4-17
 * Time: 下午9:57
 */
use App\User;

class PersonalControllerTest extends TestCase
{
    /**
     * 进行测试的初始化操作
     */
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');

        $this->getTestUser(true);
    }

    /**
     * 测试方法： getInfo
     */
    public function testGetInfo()
    {
        // 浏览器访问
        $resp = $this->call('get', 'personal/info');
        $this->assertResponseOk();
        $view = $resp->original;
        $keys = ['nickname', 'username', 'cellphone_number', 'email', 'description', 'address'];
        foreach($keys as $currentKey){
            $this->assertNotNull( $view[$currentKey] );
        }
        $this->assertEquals(Auth::user()['username'], $view['username']);

        // 移动端访问
        $resp = $this->callWantJson('get', 'api/personal/info');
        $this->assertJsonResponse($resp, 200);
        $respData = $resp->getData(true);
        $keys = ['nickname', 'username', 'cellphone_number', 'email', 'description', 'address'];
        $this->arrayMustHasKeys($respData, $keys, true);
        $this->assertEquals(Auth::user()['username'], $respData['username']);
    }

    /**
     * 测试postInfo方法
     * 用例描述：浏览器修改个人信息，信息数据不合法，重新跳转至个人信息页并显示错误信息
     * 备注：暂时只验证email字段
     */
    public function testPutInfoWebInvalid()
    {
        $putData['email'] = 'myemail.com';
        $putData['sex'] = 'male';
        $putData['description'] = 'wrong data';

        $resp = $this->call('put', 'personal/info', $putData);
        $this->assertEquals(302, $resp->getStatusCode());
        $this->assertNotEquals($putData['email'], Auth::user()['email']);
    }

    /**
     * 测试putInfo方法
     * 用例描述：浏览器修改个人信息，修改成功，返回修改后的个人信息
     */
    public function testPutInfoWebRight()
    {
        $this->seed('UserTableTestSeeder');
        $testUser = User::firstOrFail();
        $this->be( $testUser );
        $putData['email'] = 'test@163.com';
        $putData['description'] = 'What the hell';

        $resp = $this->call('put', 'personal/info', $putData);
        $this->assertResponseOk();

        $newUser = User::find($testUser['_id']);
        $this->arraySectionEquals($newUser->toArray(), $putData);
    }

    /**
     * 测试putInfo方法
     * 用例描述：App修改个人信息，修改失败，返回400响应
     */
    public function testPutInfoAppInvalid()
    {
        $putData['email'] = 'myemail.com';
        $putData['sex'] = 'male';
        $putData['description'] = 'wrong data';

        $resp = $this->callWantJson('put', 'api/personal/info', $putData);
        $this->assertJsonResponse($resp, 400);
    }

    /**
     * 测试putInfo方法
     * 用例描述：App修改个人信息，修改成功
     */
    public function testPutInfoAppRight()
    {
        $this->seed('UserTableTestSeeder');
        $testUser = User::firstOrFail();
        $this->be( $testUser );
        $putData['email'] = 'test@163.com';
        $putData['description'] = 'What the hell';
        $putData['sex'] = '女';

        $resp = $this->callWantJson('put', 'api/personal/info', $putData);
        $this->assertJsonResponse($resp, 200);
        $respData = $resp->getData(true);
        $keys = ['nickname', 'username', 'cellphone_number', 'email', 'description', 'address'];
        $this->arrayMustHasKeys($respData, $keys);
        $this->arrayMustHasEqualKeyValues($putData, Auth::user()->toArray(), array_keys($putData));
        $this->arrayMustHasEqualKeyValues($putData, $respData, array_keys($putData));
    }

    /**
     * 测试方法： getPassword
     *
     * 用例描述： 若通过浏览器访问，则返回密码的修改页面，移动端不可访问
     */
    public function testGetPassword()
    {
        $this->call('get', 'personal/password');
        $this->assertResponseOk();

        $resp = $this->callWantJson('get', 'api/personal/password');
        $this->assertJsonResponse($resp, 404);
    }

    /**
     * 测试 putPassword方法
     * 用例描述：修改密码，提交的数据不合法，重新跳转至个人信息页并显示错误信息
     */
    public function testPutPasswordInvalid()
    {
        $putData['password'] = '123456';
        $putData['newPassword'] = '1234';
        $putData['newPassword_confirmation'] = '1234';

        // 原密码错误
        $resp = $this->call('put', 'personal/password', $putData);
        $this->assertEquals(302, $resp->getStatusCode());
        $resp = $this->callWantJson('put', 'personal/password', $putData);
        $this->assertJsonResponse($resp, 400);

        // 新密码不合法
        $putData['password'] = 'spatra';
        $resp = $this->call('put', 'personal/password', $putData);
        $this->assertEquals(302, $resp->getStatusCode());
        $resp = $this->callWantJson('put', 'personal/password', $putData);
        $this->assertJsonResponse($resp, 400);

        // 确认密码不匹配
        $putData['newPassword'] = '123456';
        $resp = $this->call('put', 'personal/password', $putData);
        $this->assertEquals(302, $resp->getStatusCode());
        $resp = $this->callWantJson('put', 'personal/password', $putData);
        $this->assertJsonResponse($resp, 400);
    }


    /**
     * 测试 putPassword方法
     * 用例描述：浏览器修改密码，密码修改成功，返回200
     */
    public function testPutPasswordWebRight()
    {
        $this->seed('UserTableTestSeeder');
        $testUser = User::firstOrFail();
        $this->be( $testUser );
        $putData['password'] = 'spatra';
        $putData['newPassword'] = 'newSpatra';
        $putData['newPassword_confirmation'] = 'newSpatra';

        $this->call('put', 'personal/password', $putData);
        $this->assertResponseOk();

        $this->call('get', 'auth/logout');
        $this->assertFalse( Auth::check() );

        $this->call('post', 'auth/login', [
            'identify' => $testUser['username'],
            'password' => $putData['newPassword']
        ]);
        $this->assertTrue( Auth::check() );

    }

    /**
     * 测试 putPassword方法
     * 用例描述：移动端修改密码，密码修改成功，返回200
     */
    public function testPutPasswordAppRight()
    {
        $this->seed('UserTableTestSeeder');
        $testUser = User::firstOrFail();
        $this->be( $testUser );
        $putData['password'] = 'spatra';
        $putData['newPassword'] = 'newSpatra';
        $putData['newPassword_confirmation'] = 'newSpatra';

        $resp = $this->callWantJson('put', 'api/personal/password', $putData);
        $this->assertJsonResponse($resp);

        $this->callWantJson('get', 'api/auth/logout');
        $this->assertFalse( Auth::check() );

        $this->callWantJson('post', 'api/auth/login', [
            'identify' => $testUser['username'],
            'password' => $putData['newPassword']
        ]);
        $this->assertTrue( Auth::check() );
    }

    /**
     * 判断$tarArray是不是与$resArray的一部分相等（即$tarArray是$resArray的子集）
     *
     * @param array $resArray 源数组
     * @param array $targetArray 目标数组
     */
    protected function arraySectionEquals(array $resArray, array $targetArray)
    {
        foreach( $targetArray as $key => $currValue ) {
           $this->assertEquals($resArray[$key], $currValue);
        }
    }
}