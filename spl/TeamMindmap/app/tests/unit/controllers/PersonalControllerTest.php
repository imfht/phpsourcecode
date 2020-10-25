<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-10-26
 * Time: 下午6:09
 */
class PersonalControllerTest extends TestCase
{

    /**
     * 执行一些初始化操作：填充数据库，模拟用户登录，实例化UserController
     */
    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');

        $this->testUser = $this->getTestUser(true);

        $this->personalController = $this->app->make('PersonalController');
    }

    /**
     * 测试方法：getInfo
     */
    public function testGetInfo()
    {
        $resp = $this->personalController->getInfo();
        $this->assertEquals(200, $resp->getStatusCode());

        $respDataArray = $resp->getData(true);
        $keys = ['id', 'username', 'email', 'description', 'created_at'];
        $this->arrayMustHasKeys($respDataArray, $keys, true);
        $this->arrayMustHasEqualKeyValues($this->testUser->toArray(), $respDataArray, $keys);
    }

    /**
     * 测试方法: putInfo
     */
    public function testPutInfo()
    {
        $putData['description'] = 'change my description';

        $this->action('put', 'PersonalController@putInfo', [], $putData);
        $this->assertResponseOk();

        $user = User::findOrFail($this->testUser['id']);
        $this->assertEquals( $putData['description'], $user['description']);

    }

    /**
     * 测试方法： putPassword，应该是成功修改了密码
     */
    public function testPutPasswordRight()
    {
        $newPassword = $this->testUser['username']. 'change';
        //此处假定测试用户的账号和密码是一样的
        $putData = [
            'password' => $this->testUser['username'],
            'newPassword' => $newPassword,
            'newPassword_confirmation' => $newPassword
        ];

        $this->action('put', 'PersonalController@putPassword', [], $putData);
        $this->assertResponseOk();
        //校验密码是否是真的更改了
        $this->assertTrue( Auth::validate([
            'username' => Auth::user()['username'],
            'password' => $newPassword
        ]));
    }


    /**
     * 测试方法： putPassword， 应该是更改失败的
     */
    public function testPutPasswordWrong()
    {

        $password = 'admin';
        $newPassword = 'changePwd';

        //新密码不一致，修改失败
        $putData = [
            'password' => $password,
            'newPassword' => $newPassword,
            'newPassword_confirmation' => $password
        ];
        $this->changePasswordFail($putData);

        //原密码不正确，修改失败
        $putData = [
            'password' => $newPassword,
            'newPassword' => $newPassword,
            'newPassword_confirmation' => $newPassword
        ];
        $this->changePasswordFail($putData);
    }

    /**
     * 内部使用，尝试改变密码，但结果应该是失败的
     * @param $putData
     */
    private function changePasswordFail($putData)
    {
        $resp = $this->action('put', 'PersonalController@putPassword', [], $putData);
        $this->assertResponseStatus(403);

        $respDataArray = $resp->getData(true);
        $this->assertTrue(is_array($respDataArray['errorMessages']));

    }


    private $testUser;  //引用模拟的用户的模型实例

    private $personalController;

}
