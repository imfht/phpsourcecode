<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-12-10
 * Time: 上午12:20
 */
class TaskEventHandlerTest extends TestCase
{
    /**
     * 进行一些初始化的操作：数据库迁移和填充、实例化TaskController
     */
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->testUser = $this->getTestUser(true);
        $this->seedDB();

        $this->taskController = $this->app->make('TaskController');
    }

    /**
     * 测试taskCreated方法
     */
    public function testTaskCreated()
    {
        $testProject = Project::where('creater_id', $this->testUser['id'])->firstOrFail();

        $postData['name'] = 'task test';
        $postData['description'] = 'created by XXX';
        $postData['expected_at'] = date('Y-m-d');
        $postData['priority_id'] = ProjectTaskPriority::firstOrFail()['id'];
        $postData['appointed_member'] = [
            'add'=> [ $this->testUser['id'] ]
        ];

        $oldCount = $this->initNotifyCount();

        $this->action('post', 'TaskController@store', [ $testProject['id'] ], $postData);
        $this->assertResponseOk();

        $this->checkCount($oldCount, $testProject['id']);
    }

    /**
     * 测试taskUpdated方法
     */
    public function testTaskUpdated()
    {
        $putData['name'] = 'change';
        $putData['description'] = 'change description';
        $putData['status_id'] = ProjectTaskStatus::getIdByName('doing');
        $putData['finished_at'] = date('Y-m-d');
        $putData['expected_at'] = date('Y-m-d');

        $oldCount = $this->initNotifyCount();

        $this->call('put', '/api/project/1/task/1', $putData);
        $this->assertResponseOk();

        $this->checkCount($oldCount, 1);
    }

    /**
     * 测试taskDestroyed方法
     */
    public function testTaskDestroyed()
    {
        $targetTask = ProjectTask::first();

        $oldCount = $this->initNotifyCount();

        $this->taskController->destroy($targetTask['project_id'], $targetTask['id']);
        $this->assertEquals(null, ProjectTask::find($targetTask['id']));

        $this->checkCount($oldCount, 1);
    }

    public function seedDB()
    {
        $this->seed('ProjectTableTestSeeder');
        $this->seed('NotificationTableTestSeeder');
        $this->seed('NotifyInboxTableTestSeeder');
        $this->seed('ProjectMemberTableTestSeeder');
        $this->seed('ProjectTaskTableSeeder');
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

    private $testType = 3;

    private $taskController;

    private $testUser;

}
