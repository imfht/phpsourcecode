<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-11-6
 * Time: 下午3:00
 */
class TaskControllerTest extends TestCase
{
    /**
     * 执行一些初始化操作：开启Session功能，填充数据库，模拟用户登录
     */
    public function setUp()
    {
        parent::setUp();

        Session::start();

        Artisan::call('migrate');
        Artisan::call('db:seed');


        $this->taskController = $this->app->make('TaskController');
    }

    /**
     * 测试TaskController的index方法
     */
    public function testIndex()
    {
        $this->seedDB();
        $targetProject = Project::first();

        $resp = $this->action('get', 'TaskController@index', [ $targetProject['id'] ], []);
        $this->assertResponseOk();

        $respDataArray = $resp->getData(true);
        $this->assertTrue(is_array($respDataArray));

        $keys = ['id', 'name', 'status_id', 'priority_id', 'expected_at'];
        $this->arrayMustHasKeys(head($respDataArray), $keys, true);
        $this->arrayMustHasKeys(head($respDataArray), ['parent_id']);
    }




    public function testIndexWithPriorityId()
    {
        $this->seedDB();
        $targetProject = Project::first();

        $query = ['group' => true, 'priority_id' => 1];
        $resp = $this->action('get', 'TaskController@index', [ $targetProject['id'] ], $query);
        $this->assertResponseOk();

        $respDataArray = $resp->getData(true);
        $this->assertTrue(is_array($respDataArray));

        $keys = ['id', 'name', 'status_id', 'priority_id', 'expected_at'];
        $this->arrayMustHasKeys(head($respDataArray['undo']), $keys, true);
        $this->arrayMustHasKeys(head($respDataArray['undo']), ['parent_id']);

        $this->assertEquals( $query['priority_id'], head($respDataArray['undo'])['priority_id']);
        $this->assertEquals( $query['priority_id'], head($respDataArray['doing'])['priority_id']);
    }

    /**
     * 测试index方法，返回数据是分组的
     */
    public function testIndexWithGroup()
    {
        $this->seedDB();
        $targetProject = Project::first();

        $resq = ['group' => true];
        $respDataArray = $this->action('get', 'TaskController@index', [ $targetProject['id'] ], $resq)->getData(true);
        $this->assertTrue(is_array($respDataArray));

        $keys = [ 'undo', 'doing', 'finished' ];
        $this->arrayMustHasKeys($respDataArray, $keys);
    }

    /**
     * 测试index方法，返回数据是不分组的
     */
    public function testIndexWithOutGroup()
    {
        $this->seedDB();
        $targetProject = Project::first();

        $respDataArray = $this->action('get', 'TaskController@index', [ $targetProject['id'] ], [])->getData(true);
        $this->assertTrue(is_array($respDataArray));

        $keys = ['id', 'name', 'status_id', 'priority_id', 'expected_at'];
        $this->arrayMustHasKeys(head($respDataArray), $keys, true);
        $this->arrayMustHasKeys(head($respDataArray), ['parent_id']);
    }

    /**
     * 测试TaskController的show方法
     */
    public function testShow()
    {
        $this->seedDB();
        $this->seed('ProjectTaskMemberTableSeeder');

        $targetTask = ProjectTask::firstOrFail();
        $testUser = $targetTask['creater'];
        $this->be($testUser);

        $rep = $this->taskController->show($targetTask['project_id'], $targetTask['id']);
        $this->assertEquals(200, $rep->getStatusCode());
        $respDataArray = $rep->getData(true);

        //必须具备这个字段，但可以为空
        $this->arrayMustHasKeys($respDataArray, ['appointed_member']);

        //这些字段必须具备，并且不能为空
        $keys = ['creater', 'sub_task', 'taskStatus', 'taskPriority', 'handler'];
        $this->arrayMustHasKeys($respDataArray, $keys, true);

        //基本信息必须具备这些字段
        $keys = ['id', 'parent_id', 'name', 'description', 'expected_at', 'finished_at', 'handler_id'];
        $this->arrayMustHasEqualKeyValues($targetTask->toArray(), $respDataArray['baseInfo'], $keys, true);

        //因为当前模拟登陆的任务的创建者，所有拥有修改的权限
        $this->assertEquals(true, $respDataArray['editable']);

        $targetCreater = $testUser->toArray();
        $userKeys = ['id', 'username', 'email'];
        $this->arrayMustHasEqualKeyValues($targetCreater, $respDataArray['creater'], $userKeys);

        $targetSubTask = ProjectTask::find($targetTask['id'])->subTask->toArray();
        $subTaskKeys = ['id', 'name'];
        $this->arrayMustHasEqualKeyValues($targetSubTask[0], $respDataArray['sub_task'][0], $subTaskKeys);

    }

    /**
     *  测试store方法，应该是成功新建的
     */
    public function testStoreRight()
    {
        list($testProject, $testUser) = $this->getTestProjectAndLogin();

        $postData['name'] = 'task test';
        $postData['description'] = 'created by XXX';
        $postData['parent_id'] = ProjectTask::firstOrFail()['id'];
        $postData['expected_at'] = date('Y-m-d');
        $postData['priority_id'] = ProjectTaskPriority::findOrFail(2)['id'];
        $postData['appointed_member'] = [
            'add'=> [ $testUser['id'] ]
        ];
        $postData['handler_id'] = $testUser['id'];

        $rep = $this->call('post', '/api/project/'.$testProject['id']. '/task', $postData);
        $this->assertResponseOk();

        $repDataArray = $rep->getdata(true);
        $newTask = ProjectTask::findOrFail($repDataArray['id']);

        $keys = ['name', 'description', 'priority_id', 'parent_id'];
        $this->arrayMustHasEqualKeyValues($postData, $newTask->toArray(), $keys);

        $this->assertNotEmpty( ProjectTask_Member::where('tasK_id', $newTask['id'])
            ->where('member_id', $testUser['id'])
            ->first()
        );
        $this->assertEquals($postData['handler_id'], $newTask['handler_id']);

    }

    /**
     * 测试store，应该是创建失败的
     */
    public function testStoreWrong()
    {
        list($testProject) = $this->getTestProjectAndLogin();

        $postData['name'] = 'task test';


        //必要信息不齐全
        $this->call('post', '/api/project/'.$testProject['id']. '/task', $postData);
        $this->assertResponseStatus(403);

        //任务优先级的id没有对应的记录
        $postData['priority_id'] = ProjectTaskPriority::count() + 10010;
        $this->call('post', '/api/project/'.$testProject['id']. '/task', $postData);
        $this->assertResponseStatus(403);


    }

    /**
     * 测试控制器方法：update
     */
    public function testUpdate()
    {
        $this->seedDB();
        $this->seed('ProjectTaskMemberTableSeeder');
        $testUser = $this->getTestUser();

        $putData['name'] = 'change';
        $putData['description'] = 'change description';
        $putData['status_id'] = ProjectTaskStatus::getIdByName('doing');
        $putData['finished_at'] = date('Y-m-d');
        $putData['expected_at'] = date('Y-m-d');
        $putData['handler_id'] = 3;

        $this->call('put', '/api/project/1/task/1', $putData);
        $this->assertResponseOk();

        $testTask = ProjectTask::findOrFail(1)->toArray();
        $keys = ['name', 'description', 'status_id', 'finished_at', 'expected_at', 'handler_id'];
        $this->arrayMustHasEqualKeyValues($putData, $testTask, $keys);
        $this->assertEquals($testTask['last_man'], $testUser['id']);
    }


    /**
     * 测试控制器方法：update，假设任务有拖放，但不需要询问是否更改负责人
     */
    public function testUpdateDragWithoutReplace()
    {
        $this->seedDB();
        $this->seed('ProjectTaskMemberTableSeeder');
        $testUser = $this->getTestUser();

        $oldTask = ProjectTask::findOrFail(3)->toArray();
        $putData['parent_id'] = 1;

        $this->call('put', '/api/project/1/task/3', $putData);
        $this->assertResponseOk();

        $testTask = ProjectTask::findOrFail( $oldTask['id'] )->toArray();
        $this->assertEquals($oldTask['handler_id'], $testTask['handler_id']);
        $this->assertEquals($putData['parent_id'], $testTask['parent_id']);
        $this->assertEquals($testTask['last_man'], $testUser['id']);
    }

    /**
     * 测试控制器方法：update，假设任务有拖放，并且应询问是否更改负责人
     */
    public function testUpdateDragWithReplace()
    {
        list($testProject, $testUser) = $this->getTestProjectAndLogin();
        $this->seed('ProjectTaskMemberTableSeeder');

        $putData['parent_id'] = 1;

        $respData = $this->call('put', '/api/project/1/task/4', $putData)->getData(true);
        $this->assertResponseOk();

        $parentTask = ProjectTask::find($putData['parent_id']);
        $testData['status'] = 'reselectHandler';
        $testData['memberList'] = $parentTask['taskMember']->toArray();
        array_push($testData['memberList'], $parentTask['handler']->toArray());
        $keys = ['status', 'memberList'];
        $this->arrayMustHasEqualKeyValues($testData, $respData, $keys);
    }

    public function testUpdateDragToTop()
    {
        list($testProject, $testUser) = $this->getTestProjectAndLogin();
        $this->seed('ProjectTaskMemberTableSeeder');

        $oldTask = ProjectTask::findOrFail(4)->toArray();
        $putData['parent_id'] = 0;

        $respData = $this->call('put', '/api/project/1/task/4', $putData);
        $this->assertResponseOk();

        $testTask = ProjectTask::find(4)->toArray();
        $this->assertEquals(null, $testTask['parent_id']);

        $testTask = ProjectTask::findOrFail(4)->toArray();
        $keys = ['name', 'description', 'status_id', 'finished_at', 'expected_at', 'handler_id'];
        $this->arrayMustHasEqualKeyValues($oldTask, $testTask, $keys);
        $this->assertEquals($testTask['last_man'], $testUser['id']);

    }

    /**
     * 测试控制器方法：destroy
     */
    public function testDestroy()
    {
        $this->seedDB();
        $testUser = $this->getTestUser();

        $targetTask = ProjectTask::first();
        $deleteCount = ProjectTask::where('id', $targetTask['id'])->orWhere('parent_id', $targetTask['id'])->count();
        $oldTaskCount = ProjectTask::count();

        $this->taskController->destroy($targetTask['project_id'], $targetTask['id']);
        $this->assertEquals($oldTaskCount - $deleteCount, ProjectTask::count());
        $this->assertEquals(0, ProjectTask_Member::where('task_id', $targetTask['id'])->count());
    }

    private function seedDB()
    {
        $this->seed('UserTableTestSeeder');
        $this->seed('ProjectTableTestSeeder');
        $this->seed('ProjectTaskTableSeeder');
    }

    private function getTestProjectAndLogin()
    {
        $this->seedDB();
        $testUser = User::firstOrFail();
        $this->be($testUser);
        $testProject = Project::where('creater_id', $testUser['id'])->firstOrFail();

        return [$testProject, $testUser];
    }


    private $taskController;
}
