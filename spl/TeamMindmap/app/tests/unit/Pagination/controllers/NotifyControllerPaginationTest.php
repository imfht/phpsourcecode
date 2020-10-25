<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-3-6
 * Time: 下午9:55
 */

/**
 * 通知控制器分页业务测试
 * Class NotifyControllerPaginationTest
 */
class NotifyControllerPaginationTest extends TestCase
{
    /**
     * 进行一些初始化的操作：数据库迁移与填充、模拟用户登陆、实例化notifyController
     */
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->testUser = $this->getTestUser(true);
        $this->seedDB();


        $this->notifyController = $this->app->make('NotifyController');
    }

    /**
     * 测试获取项目列表清单
     */
    public function testGetProjects()
    {
        $this->seed('ProjectTaskTableSeeder');
        $this->seed('SharingTableTestSeeder');

        $resp = $this->action('get', 'NotifyController@index', [], ['per_page' => 2]);
        $this->assertResponseOk();

        $respData = $resp->getData(true);
        $keys = [ 'total', 'per_page', 'current_page', 'last_page', 'from', 'to', 'data' ];
        $this->arrayMustHasKeys( $respData, $keys );
        $this->assertEquals( 2, count($respData['data']));
    }

    /**
     * 测试根据项目id获取项目
     */
    public function testGetProjectWithProjectId()
    {

        $this->seed('ProjectTaskTableSeeder');
        $this->seed('SharingTableTestSeeder');

        $testProject = $this->testUser->createProjects->first();
        $resp = $this->action('get', 'NotifyController@index', [ $testProject['id'] ], ['per_page' => 1] );
        $this->assertResponseOk();

        $respData = $resp->getData(true);
        $keys = [ 'total', 'per_page', 'current_page', 'last_page', 'from', 'to', 'data' ];
        $this->arrayMustHasKeys( $respData, $keys );
        $this->assertEquals( 1, count($respData['data']));
    }



    public function seedDB()
    {
        $this->seed('ProjectTableTestSeeder');
        $this->seed('NotificationTableTestSeeder');
        $this->seed('NotifyInboxTableTestSeeder');
    }

    protected $notifyController;

    protected $testUser;
}
