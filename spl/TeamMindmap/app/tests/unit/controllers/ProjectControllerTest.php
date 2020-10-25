<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-10-25
 * Time: 上午10:16
 */

/**
 * Class ProjectControllerTest
 *
 * 此类用于测试控制器: ProjectController
 */
class ProjectControllerTest extends TestCase{

    /**
     * 执行一些初始化操作: 填充数据库，模拟用户登陆， 实例化ProjectController
     */
    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->testUser = $this->getTestUser(true);
        $this->projectController = $this->app->make('ProjectController');
    }


    public function testIndex()
    {
        $this->seedDB();

        $resq = ['option' => 'all'];
        $resp = $this->action('get', 'ProjectController@index', null ,$resq);
        $this->assertEquals(200, $resp->getStatusCode());
        $respDataArray = $resp->getData(true);

        $keys = ['id', 'name', 'cover', 'introduction', 'creater_id', 'created_at', 'updated_at'];
        $this->arrayMustHasKeys(head($respDataArray), $keys);

        $this->assertCount(2, $respDataArray);
    }


    /**
     * 测试ProjectController的show方法
     */
    public function testShow()
    {
        $this->seedDB();

        $testProject = $this->testUser->createProjects()->firstOrFail();
        $resp = $this->projectController->show($testProject['id']);
        $this->assertEquals(200, $resp->getStatusCode());
        $respDataArray = $resp->getData(true);

        $keys = ['baseInfo', 'editable', 'creater', 'members'];
        $this->arrayMustHasKeys($respDataArray, $keys, true);

        $keys = ['id', 'name', 'cover', 'introduction', 'created_at', 'updated_at', 'creater_id'];
        $this->arrayMustHasEqualKeyValues($testProject->toArray(), $respDataArray['baseInfo'], $keys);

        $keys = ['id', 'username', 'email', 'created_at'];
        $this->arrayMustHasEqualKeyValues($this->testUser->toArray(), $respDataArray['creater'], $keys);

        $this->assertCount( count($testProject['members']->toArray()), $respDataArray['members']);
	}

    /**
     * 测试ProjectController的destroy方法
     */
    public function testDestroy()
    {
        $this->seedDB();
        $targetProject = Project::first();
        $oldCount = Project::count();

        $resp = $this->projectController->destroy( $targetProject['id'] );
        $this->assertEquals(200, $resp->getStatusCode());

        $this->assertEquals( $oldCount - 1, Project::count());
        //删除项目后，所关联的ProjectMemberTable对应条目应该也被删除
        $this->assertEquals(0, Project_Member::where('project_id', $targetProject['id'])->get()->count() );
    }

    /**
     * 测试ProjectController的store方法，操作是成功的
     */
    public function testStoreRight()
    {
        $testMember = User::where('id', '<>', $this->testUser['id'])->firstOrFail();
        $postData['name'] = 'UnitTest in Project';
        $postData['introduction'] = 'unit test';
        $postData['memberList'] = [
          ['user_id'=>$testMember['id'], 'role_id'=>1 ]
        ];

        $respDataArray = $this->action('post', 'ProjectController@store', [], $postData)->getData(true);
        $this->assertResponseOk();

        $newProject = $this->testUser->createProjects()->firstOrFail();
        $keys = ['name', 'introduction'];
        $this->arrayMustHasEqualKeyValues($newProject->toArray(), $postData, $keys);
        $this->assertNotEmpty(
            Project_Member::where('project_id', $respDataArray['id'])
                ->where('member_id', $testMember['id'])
                ->first()
        );
    }

    /**
     * 测试方法：store， 应该是操作失败的
     */
    public function testStoreWrong()
    {
        $postData['name'] = 'UnitTest in Project';
        $postData['introduction'] = 'unit test';

        unset($postData['name']);
        $this->action('post', 'ProjectController@store', [], $postData);
        $this->assertResponseStatus(403);

        $postData['name'] = 'UnitTest in Project';
        unset( $postData['introduction'] );
        $this->action('post', 'ProjectController@store', [], $postData);
        $this->assertResponseStatus(403);
    }

    /**
     * 测试ProjectController的update方法
     */
    public function testUpdate()
    {
        $this->seedDB();
        $testProject = $this->testUser->createProjects()->firstOrFail();

        $projectId = $testProject['id'];
        $putData['name'] = 'change project name';
        $putData['introduction'] = 'change project introduction';

        $this->action('put', 'ProjectController@update', [$projectId], $putData);
        $this->assertResponseOk();

        $editedProject = Project::findOrFail($projectId);
        $this->arrayMustHasEqualKeyValues($editedProject->toArray(), $putData, array_keys($putData));
    }

    /**
     * 内部使用，用于加载测试所用的填充类,该类以 TableTestSeeder 结尾，
     * 此方法会自动补全填充类的类名.
     *
     * @param $testSeederName
     */
    protected function seedTestSeeder($testSeederName)
    {
        $this->seed($testSeederName. 'TableTestSeeder');
    }

    private function seedDB()
    {
        $this->seedTestSeeder('Project');
        $this->seedTestSeeder('ProjectMember');
    }

    private $projectController;	//引用控制器ProjectController的实例

    private $testUser;
}