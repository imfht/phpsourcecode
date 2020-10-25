<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-4-11
 * Time: 下午9:53
 */

class AuthControllerTest extends TestCase
{
    /**
     * 进行测试的初始化工作
     */
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');
        Artisan::call('db:seed');
    }

    /**
     * 测试postLogin方法
     * 用例描述：浏览器登陆，该用户不存在，跳转到登录页面，并显示相关错误提示
     */
    public function testPostLoginWebWrong()
    {
        $postData = $this->buildLoginData('zero', '123456');
        $shouldRedirect = url('auth/login');

        $resp = $this->call('post', 'auth/login', $postData);
        $this->assertFalse( Auth::check() );
        $this->assertEquals($shouldRedirect, $resp->headers->get('location'));
    }

    /**
     * 测试postLogin方法
     * 用例描述：浏览器登陆，用户名不合法，跳转到登录页面，并显示错误提示
     */
    public function testPostLoginUsernameWebInvalid()
    {
        $postData = $this->buildLoginData('6ads', '1234');

        $resp = $this->call('post', 'auth/login', $postData);
        $this->assertFalse( Auth::check() );
        $this->assertEquals(302, $resp->getStatusCode());
    }

    /**
     * 测试postLogin方法
     * 用例描述：浏览器登录，手机号码不合法，跳转到登录页面，并显示错误提示
     */
    public function testPostLoginCellphone_NumberWebInvalid()
    {
        $postData = $this->buildLoginData('1364823', '1234');

        $resp = $this->call('post', 'auth/login', $postData);
        $this->assertFalse( Auth::check() );
        $this->assertEquals(302, $resp->getStatusCode());
    }

    /**
     * 测试postLogin方法
     * 用例描述：浏览器登陆，通过用户名的方式登录成功，跳转至首页
     */
    public function testPostLoginUsernameWebRight()
    {
        $this->seed('UserTableTestSeeder');
        $testUser = \App\User::firstOrFail();
        // 这里使用$testUser['password']会出错，暂时直接写密码
        $postData = $this->buildLoginData($testUser['username'], 'spatra');
        $shouldRedirect = url('/');

        $resp = $this->call('post', 'auth/login', $postData);
        $this->assertTrue( Auth::check() );
        $this->assertEquals($shouldRedirect, $resp->headers->get('location'));
    }

    /**
     * 测试postLogin方法
     * 用例描述：浏览器登录，通过手机号码的方式登录成功，跳转至首页
     */
    public function testPostLoginCellphone_NumberWebRight()
    {
        $this->seed('UserTableTestSeeder');
        $testUser = \App\User::firstOrFail();
        $postData = $this->buildLoginData($testUser['cellphone_number'], 'spatra');
        $shouldRedirect = url('/');

        $resp = $this->call('post', 'auth/login', $postData);
        $this->assertTrue( Auth::check() );
        $this->assertEquals($shouldRedirect, $resp->headers->get('location'));
    }

    /**
     * 测试postLogin方法
     * 用例描述：移动端登录，该用户不存在，返回400
     */
    public function testPostLoginAppWrong()
    {
        $postData = $this->buildLoginData('zero', '123456');

        $resp = $this->callWantJson('post', 'api/auth/login', $postData);
        $this->assertFalse( Auth::check() );
        $this->assertJsonResponse($resp, 400);
    }

    /**
     * 测试postLogin方法
     * 用例描述：移动端登录，用户名不合法，返回400
     */
    public function testPostLoginUsernameAppInvalid()
    {
        $postData = $this->buildLoginData('6ads', '123');

        $resp = $this->callWantJson('post', 'api/auth/login', $postData);
        $this->assertJsonResponse($resp, 400);
        $this->assertFalse( Auth::check() );
    }

    /**
     * 测试postLogin方法
     * 用例描述：移动端登录，手机号码不合法，返回400
     */
    public function testPostLoginCellphone_NumberAppInvalid()
    {
        $postData = $this->buildLoginData('1364823', '123');

        $resp = $this->callWantJson('post', 'api/auth/login', $postData);
        $this->assertJsonResponse($resp, 400);
        $this->assertFalse( Auth::check() );
    }

    /**
     * 测试postLogin方法
     * 用例描述：移动端登录，通过用户名的方式登录成功，返回用户的基本信息
     */
    public function testPostLoginUsernameAppRight()
    {
        $this->seed('UserTableTestSeeder');
        $testUser = \App\User::firstOrFail();
        $postData =$this->buildLoginData($testUser['username'], 'spatra');

        $resp = $this->callWantJson('post', 'api/auth/login', $postData);
        $this->assertJsonResponse($resp, 200);
        $this->assertTrue( Auth::check() );

        // 测试返回数据
        $respData = $resp->getData(true);
        $keys = ['username', 'cellphone_number', 'nickname', 'sex'];
        $this->arrayMustHasKeys($respData, $keys);
        $this->arrayMustHasEqualKeyValues($testUser->toArray(), $respData, $keys);
    }

    /**
     * 测试postLogin方法
     * 用例描述：移动端登录，通过手机号码的方式登录成功，返回用户的基本信息
     */
    public function testPostLoginCellphone_NumberAppRight()
    {
        $this->seed('UserTableTestSeeder');
        $testUser = \App\User::firstOrFail();
        $postData =$this->buildLoginData($testUser['cellphone_number'], 'spatra');

        $resp = $this->callWantJson('post', 'api/auth/login', $postData);
        $this->assertJsonResponse($resp, 200);
        $this->assertTrue( Auth::check() );

        // 测试返回数据
        $respData = $resp->getData(true);
        $keys = ['username', 'cellphone_number', 'nickname', 'sex'];
        $this->arrayMustHasKeys($respData, $keys);
        $this->arrayMustHasEqualKeyValues($testUser->toArray(), $respData, $keys);
    }

    /**
     * 构建用户登录的测试数据 
     *
     * @param $identify string 用户名或手机号
     * @param $password string 用户的密码
     * @return array 包换用户名（手机号）、密码的关联数组
     */
    protected function buildLoginData($identify, $password)
    {
        return [
            'identify' => $identify,
            'password' => $password
        ];
    }

    /**
     * 测试postRegister方法
     * 用例描述：浏览器注册，用户名不合法，注册失败，重新跳转到注册页并显示相关错误信息
     */
    public function testPostRegisterWebUserNameInvalid()
    {
        $postData = [
            'username' => '6name',
            'password' => 'testPasswd',
            'password_confirmation' => 'testPasswd'
        ];
        $shouldRedirect = url('/auth/register');

        $resp = $this->call('post', '/auth/register', $postData);
        $this->assertFalse( Auth::check() );
        $this->assertEquals(302, $resp->getStatusCode());
        $this->assertEquals($shouldRedirect, $resp->headers->get('location'));
    }

    /**
     * 测试postRegister方法
     * 用例描述：浏览器注册，手机号不合法，注册失败，重新跳转到注册页并显示相关错误信息
     */
    public function testPostRegisterWebCellPhone_NumberInvalid()
    {
        $postData = [
            'cellphone_number' => '15623',
            'password' => 'testPsswd',
            'password_confirmation' => 'testPasswd'
        ];
        $shouldRedirect = url('auth/register');

        $resp = $this->call('post', 'auth/register', $postData);
        $this->assertFalse( Auth::check() );
        $this->assertEquals(302, $resp->getStatusCode());
        $this->assertEquals($shouldRedirect, $resp->headers->get('location'));
    }

    /**
     * 测试postRegister方法
     * 用例描述：浏览器注册，通过用户名的方式注册成功，跳转到home页面
     */
    public function testPostRegisterUsernameWebRight()
    {
        $postData = [
            'username' => 'admin112',
            'password' => 'testPasswd',
            'password_confirmation' => 'testPasswd'
        ];
        $shouldRedirect = url('/');

        $resp = $this->call('post', 'auth/register', $postData);
        $this->assertTrue( Auth::check() );
        $this->assertEquals(302, $resp->getStatusCode());
        $this->assertEquals($shouldRedirect, $resp->headers->get('location'));

        $this->dropTestingDatabase();
    }

    /**
     * 测试postRegister方法
     * 用例描述：浏览器注册，通过手机号码的方式注册成功，跳转到home页面
     */
    public function testPostRegisterCellphone_NumberWebRight()
    {
        $postData = [
            'cellphone_number' => '15625012345',
            'password' => 'testPasswd',
            'password_confirmation' => 'testPasswd'
        ];
        $shouldRedirect = url('/');

        $resp = $this->call('post', 'auth/register', $postData);
        $this->assertTrue( Auth::check() );
        $this->assertEquals(302, $resp->getStatusCode());
        $this->assertEquals($shouldRedirect, $resp->headers->get('location'));

        $this->dropTestingDatabase();
    }

    /**
     * 测试postRegister方法
     * 用例描述：移动端注册，用户名不合法，返回400，以及相关的错误信息
     */
    public function testPostRegisterAppUserNameInvalid()
    {
        $postData = [
            'username' => '6name',
            'password' => 'testPasswd',
            'password_confirmation' => 'testPasswd'
        ];

        $resp = $this->callWantJson('post', 'api/auth/register', $postData);
        $this->assertFalse( Auth::check() );
        $this->assertJsonResponse($resp, 400);
        $respData = $resp->getData(true);
        $keys = [ 'username' ];
        $this->arrayMustHasKeys($respData, $keys);
    }

    /**
     * 测试postRegister方法
     * 用例描述：移动端注册，手机号不合法，返回400,以及相关的错误信息
     */
    public function testPostRegisterAppCellPhone_NumberInvalid()
    {
        $postData = [
            'cellphone_number' => '12343',
            'password' => 'testPasswd',
            'password_confirmation' => 'testPasswd'
        ];

        $resp = $this->callWantJson('post', 'api/auth/register', $postData);
        $this->assertFalse( Auth::check() );
        $this->assertJsonResponse($resp, 400);
        $respData = $resp->getData(true);
        $keys = [ 'cellphone_number' ];
        $this->arrayMustHasKeys($respData, $keys);
    }

    /**
     * 测试postRegister方法
     * 用例描述：移动端注册，通过用户名的方式注册成功，返回201，以及用户的基本信息
     */
    public function testPostRegisterUsernameAppRight()
    {
        $postData = [
            'username' => 'admin111',
            'password' => 'testPasswd',
            'password_confirmation' => 'testPasswd'
        ];

        $resp = $this->callWantJson('post', 'api/auth/register', $postData);
        $this->assertTrue( Auth::check() );
        $this->assertJsonResponse($resp, 201);

        $respData = $resp->getData(true);
        $keys = [ 'username', 'head_image' ];
        $this->arrayMustHasKeys($respData, $keys);

        $this->dropTestingDatabase();
    }

    /**
     * 测试postRegister方法
     * 用例描述：移动端注册，通过手机号码的方式注册成功，返回201，以及用户的基本信息
     */
    public function testPostRegisterCellphone_NumberAppRight()
    {
        $postData = [
            'cellphone_number' => '15625012367',
            'password' => 'testPasswd',
            'password_confirmation' => 'testPasswd'
        ];

        $resp = $this->callWantJson('post', 'api/auth/register', $postData);
        $this->assertTrue( Auth::check() );
        $this->assertJsonResponse($resp, 201);

        $respData = $resp->getData(true);
        $keys = [ 'username', 'head_image' ];
        $this->arrayMustHasKeys($respData, $keys);

        $this->dropTestingDatabase();
    }

}