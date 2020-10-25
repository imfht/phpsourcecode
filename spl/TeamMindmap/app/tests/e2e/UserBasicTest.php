<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-10-27
 * Time: 下午7:52
 */

class UserBasicTest extends TestCase{

	/**
	 * 进行数据库的初始化工作
	 */
	public function setUp(){
		parent::setUp();

		Artisan::call('migrate');

		//设定跟随跳转
		$this->client->followRedirects();
	}

	/**
	 * 测试注册，并使用最新注册账号进行登陆、登出操作.
	 */
	public function testSigninAndLogin(){
		/*
		 * 设置将要注册的用户信息
		 */
		$username = 'UnitTest';
		$email = 'unitTest@test.com';
		$password = 'unit-test-2014';

		//访问首页，并选择注册按钮，进入注册页
		$crawler = $this->client->request('get', '/');
		$this->assertResponseOk();
		$link = $crawler->selectLink('注册')->link();
		$crawler = $this->client->click($link);

		//填写注册信息，进行注册操作
		$signinForm = $crawler->selectButton('注册')->form();
		$signinForm->setValues([
			'username'=>$username,
			'email'=>$email,
			'password'=>$password,
			'password_confirmation'=>$password
		]);
		$this->client->submit($signinForm);

		//若注册成功，应该是已经登陆状态了
		$this->assertTrue( Auth::check() );

		//退出登陆
		$this->client->request('get', '/authority/logout');
		$this->assertResponseOk();
		$this->assertFalse( Auth::check() );

		//在首页选择登入按钮，跳转到登陆页面
		$crawler = $this->client->request('get', '/');
		$this->assertResponseOk();
		$link = $crawler->selectLink('登入')->link();
		$crawler = $this->client->click($link);

		//用刚注册的账号进行登陆操作
		$loginForm = $crawler->selectButton('登入')->form();
		$loginForm->setValues([
			'identify'=>$username,
			'password'=>$password
		]);
		$this->client->submit($loginForm);
		$this->assertTrue( Auth::check() );

		//在登陆后的导航栏中, 再次进行登出操作
		$this->client->request('get', '/authority/logout');
		$this->assertResponseOk();
		$this->assertFalse( Auth::check() );
	}

	/**
	 * 输入不符合规定的申请数据，校验过滤.
	 */
	public function testSigninValidation(){

		//符合格式的注册用户信息
		$username = 'UnitTest';
		$email = 'unitTest@test.com';
		$password = 'unit-test-2014';


		$crawler = $this->submitSigninForm([
			'username'=>$username,
			'email'=>'admin@com',
			'password'=>$password,
			'password_confirmation'=>$password
		]);
		$this->assertCount(1, $crawler->filter('title:contains(注册)'));
		$this->assertFalse( Auth::check() );

		$crawler = $this->submitSigninForm([
			'username'=>$username,
			'email'=>$email,
			'password'=>$password,
			'password_confirmation'=>$password. 'password'
		]);
		$this->assertCount(1, $crawler->filter('title:contains(注册)'));
		$this->assertFalse( Auth::check() );
	}

	protected function submitForm($uri, $formSubmitButtonName, $formData){
		$crawler = $this->client->request('get', $uri);
		$this->assertResponseOk();
		$form = $crawler->selectButton($formSubmitButtonName)->form();

		$form->setValues($formData);

		return $this->client->submit($form);
	}

	protected function submitSigninForm($fomData){
		return $this->submitForm('/authority/signin', '注册', $fomData);
	}
}