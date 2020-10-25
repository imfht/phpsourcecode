<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-12-9
 * Time: 下午3:31
 */
class ProjectEventHandlerTest extends TestCase
{
    /**
     * 进行一些初始化的操作：数据库迁移和填充、用户登陆、实例化ProjectController
　　 *
     */
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->testUser = $this->getTestUser(true);
        $this->seedDB();

        $this->projectController = $this->app->make('ProjectController');
    }

    /**
     * 测试projectCreated方法
     */
    public function testProjectUpdated()
    {
        $testProject = $this->testUser->createProjects()->firstOrFail();

        $projectId = $testProject['id'];
        $putData['name'] = 'change project name';
        $putData['introduction'] = 'change project introduction';

        $oldCount = $this->initNotifyCount();

        $this->action('put', 'ProjectController@update', [$projectId], $putData);
        $this->assertResponseOk();

        $this->checkCount($oldCount, $projectId);
    }

    /**
     * 测试projectDestroy方法
     */
    public function testProjectDestroy()
    {
        $targetProject = Project::first();

        $oldCount = $this->initNotifyCount();
        $oldCount['inbox'] += Project_Member::where('project_id', $targetProject['id'])->count();

        $resp = $this->projectController->destroy( $targetProject['id'] );
        $this->assertEquals(200, $resp->getStatusCode());

        $newCount['notify'] = Notification::where('id', $targetProject['id'])->count();
        $this->assertEquals(0, $newCount['notify']);

        $newCount['inbox'] = Project_Member::where('project_id', $targetProject['id'])->count();
        $inboxCount = NotifyInbox::count();
        $this->assertEquals(0, $inboxCount);

    }

    /**
     * 进行数据库填充
     */
    public function seedDB()
    {
        $this->seed('ProjectTableTestSeeder');
        $this->seed('NotificationTableTestSeeder');
        $this->seed('ProjectMemberTableTestSeeder');
    }

    /**
     * 获得通知添加之前的Notification和NotifyInbox的记录数，用于验证是否成功添加通知
     *
     * @return $oldCount array 关联数组，存储Notification和NotifyInbox中的记录数 
     */
    public function initNotifyCount()
    {
        $oldCount['notify'] = Notification::where('type_id', $this->testType)->count();
        $oldCount['inbox'] = NotifyInbox::count();
        return $oldCount;
    }

    /**
     * 通过对比记录数目，检查是否成功添加通知记录
     *
     * @param $oldCount array 关联数组，存储Notification和NotifyInbox中的记录数
     * @param $sourceId int 暂定为项目的id
     */
    public function checkCount($oldCount, $sourceId)
    {
        $newCount = Notification::where('type_id', $this->testType)->count();
        $this->assertEquals($oldCount['notify'] + 1, $newCount);

        $memberCount = Project_Member::where('project_id', $sourceId)->count();
        $inboxCount = NotifyInbox::count();
        $this->assertEquals($oldCount['inbox'] + $memberCount + 1, $inboxCount);
    }

    private $testType = 2;

    private $projectController;

    private $testUser;
}
