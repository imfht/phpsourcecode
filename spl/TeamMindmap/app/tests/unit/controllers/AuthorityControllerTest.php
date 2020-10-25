<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-10-27
 * Time: 下午4:57
 */
class AuthorityControllerTest extends TestCase
{
    /**
     * 执行一些初始化操作：开启Session功能，填充数据库，实例化AuthorityController
     */
    public function setUp()
    {
        parent::setUp();

        Session::start();

        Artisan::call('migrate');

        $this->seed('UserTableTestSeeder');

        $this->authorityController = $this->app->make('AuthorityController');

    }

    /**
     * 测试AuthorityController的postLogin方法
     */
    public function testLogin()
    {

        $postData['identify'] = 'admin';
        $postData['password'] = 'admin';
        $resp = $this->call('post', '/authority/login', $postData, [], ['HTTP_REFERER'=>URL::to('/authority/login')]);
        $this->assertRedirectedTo('/ng#/project');
        $this->call('get', '/authority/logout');
        $this->assertFalse( Auth::check() );
    }

    /**
     * 测试AuthorityController的postSignin方法
     */
    public function testSignin()
    {

        $postData['username'] = 'zero';
        $postData['password'] = '123456';
        $postData['password_confirmation'] = '123456';
        $postData['email'] = 'zero@163.com';
        $this->call('post', '/authority/signin', $postData);
        $this->assertTrue( Auth::check() );
        $this->action('get', 'AuthorityController@getLogout');
        $this->assertFalse( Auth::check() );
        $postData['identify'] = 'zero';
        $this->call('post', '/authority/login', $postData);
        $this->assertTrue( Auth::check() );
    }

    private $authorityController;
}
