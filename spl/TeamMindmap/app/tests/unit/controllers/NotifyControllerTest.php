<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-12-4
 * Time: 下午4:12
 */
class NotifyControllerTest extends TestCase
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
     * 测试index方法，没有传递参数project_id
     */
    public function testIndexWithoutProjectId()
    {
        $this->seed('ProjectTaskTableSeeder');
        $this->seed('SharingTableTestSeeder');

        $resp = $this->notifyController->index();
        $this->assertEquals(200,$resp->getStatusCode());

        $respData = $resp->getData(true);
        $keys = ['id', 'type_id', 'title', 'content', 'trigger_id', 'source_id', 'read'];
        $firstData = head($respData);
        $notification = Notification::getSpecifiedBuilder($this->testUser['id'], $firstData['type_id'], $firstData['project_id'])
            ->get()->toArray();
        $this->arrayMustHasEqualKeyValues( head($notification), $firstData, $keys);
        $this->checkTriggerData( $firstData['trigger'] );
    }

    /**
     * 测试index方法，通过查询字符串传递project_id来获取某一项目的所有通知
     */
    public function testIndexWithProjectId()
    {
        $this->seed('ProjectTaskTableSeeder');
        $this->seed('SharingTableTestSeeder');

        $testProject = $this->testUser->createProjects->first();

        $resp = $this->action('get', 'NotifyController@index', [], [ 'project_id' => $testProject['id'] ]);
        $this->assertResponseOk();

        $respData = $resp->getData(true);
        $keys = ['id', 'type_id', 'title', 'content', 'trigger_id', 'source_id', 'read'];
        $firstData = head($respData);

        $notification = Notification::getSpecifiedBuilder($this->testUser['id'], $firstData['type_id'], $firstData['source_id'])->get()->toArray();
        $this->arrayMustHasEqualKeyValues( head($notification), $firstData, $keys);

        $this->checkTriggerData( $firstData['trigger'] );
    }

    /**
     * 测试index方法，暂时针对现有的数据填充来测试，返回的数据应该是某个项目的已读的通知数据
     */
    public function testIndexWithProjectId_read()
    {
        $this->seed('ProjectTaskTableSeeder');
        $this->seed('SharingTableTestSeeder');

        $testProject = $this->testUser->createProjects->first();
        $resp = $this->action('get', 'NotifyController@index', [], [ 'project_id' => $testProject['id'], 'read' => 1 ]);
        $this->assertResponseOk();

        $respData = $resp->getData(true);
        $this->assertEmpty($respData);
    }

    public function testIndexWithProject_unread()
    {
        $this->seed('ProjectTaskTableSeeder');
        $this->seed('SharingTableTestSeeder');

        $testProject = $this->testUser->createProjects->first();
        $resp = $this->action('get', 'NotifyController@index', [], [ 'project_id' => $testProject['id'], 'read' => 0 ]);
        $this->assertResponseOk();

        $respData = $resp->getData(true);
        $firstData = head($respData);
        $this->assertEquals(0, $firstData['read']);
    }


    /**
     * 测试show方法
     */
    public function testShow()
    {
        //暂不对系统通知做测试

        //测试项目通知
        $this->checkSourceNotify(1, 'project', 'Project');

        //测试任务通知
        $this->seed('ProjectTaskTableSeeder');
        $this->checkSourceNotify(2, 'task', 'ProjectTask');

        //测试分享通知
        $this->seed('SharingTableTestSeeder');
        $this->checkSourceNotify(3, 'project_sharing', 'ProjectSharing');
    }

    /**
     *
     * 用于show方法的测试
     * @param $id
     * @param string $sourceName 对应通知表notifyTypes中的name字段
     * @param $modelName
     */
    public function checkSourceNotify($id, $sourceName, $modelName)
    {
        $currNotify = Notification::find($id)->toArray();
        $currNotify['read'] = true;
        $resp = $this->notifyController->show($currNotify['id']);
        $this->assertEquals(200, $resp->getStatusCode());

        $respData = $resp->getData(true);

        $keys = ['id', 'type_id', 'title', 'content', 'trigger_id', 'source_id', 'read'];
        $this->arrayMustHasEqualKeyValues($currNotify, $respData, $keys);

        $keys = ['id', 'name'];
        $currProject = $modelName::find($respData['remark'][$sourceName]['id'])->toArray();
        $this->arrayMustHasEqualKeyValues($currProject, $respData['remark'][$sourceName], $keys);

        $this->checkTriggerData( $respData['trigger'] );
    }

    /**
     * 检查trigger的数据是否正确
     *
     * @param $resTrigger array 关联数组，待检查的trigger数据
     */
    public function checkTriggerData($resTrigger)
    {
        $trigger = User::find( $resTrigger['id'] )->toArray();
        $keys = ['id', 'username', 'email', 'description', 'head_image'];
        $this->arrayMustHasEqualKeyValues($trigger, $resTrigger, $keys);
    }

    /**
     * 测试update方法，操作为正确的情况
     */
    public function testUpdateRight()
    {
        $currNotify = Notification::getUserNotify($this->testUser['id'], 0);
        $resp = $this->notifyController->update( head($currNotify)['id']);
//        $this->assertEquals(200, $resp->getStatusCode());
    }

    /**
     * 测试update方法，操作为错误的情况
     */
    public function testUpdateWrong()
    {
        $currNotify = 999;
        $resp = $this->notifyController->update($currNotify['id']);
        $this->assertEquals(500, $resp->getStatusCode());
    }

    /**
     * 测试destroy方法，操作为正确的情况
     */
    public function testDestroyRight()
    {
        $currNotify = Notification::getUserNotify($this->testUser['id'], 0);
        $resp = $this->notifyController->destroy( head($currNotify)['id']);
        $this->assertEquals(200, $resp->getStatusCode());
    }

    /**
     * 测试destroy方法，操作为错误的情况
     */
    public function testDestroyWrong()
    {
        $currNotify = 999;
        $resp = $this->notifyController->destroy($currNotify['id']);
        $this->assertEquals(500, $resp->getStatusCode());
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
