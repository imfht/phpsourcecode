<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-1-28
 * Time: 下午4:37
 */

/**
 * 项目中分享模块测试
 * Class ProjectSharingControllerTest
 */
class ProjectSharingControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->testUser = $this->getTestUser(true);

        $this->seed('ProjectTableTestSeeder');
        $this->seed('SharingTableTestSeeder');

        $this->ProjectSharingController = $this->app->make('ProjectSharingController');
    }

    /**
     * 获取分享列表清单不分页测试
     */
    public function testWithoutIndexTest()
    {
        $this->seedDB();
        $testProject = Project::firstOrFail();
        $resp = $this->action('get', 'ProjectSharingController@index', [$testProject['id']])->getData(true);

        $sharingKeys = ['id', 'name', 'content', 'project_id', 'created_at', 'updated_at', 'creater', 'tags', 'resources'];
        $this->arrayMustHasKeys(head($resp), $sharingKeys);

        $createrKeys = ['id', 'username', 'email', 'description', 'head_image'];
        $this->arrayMustHasKeys(head($resp)['creater'], $createrKeys);

        if (isset(head($resp)['tags'])) {
            $createrKeys = ['id', 'name', 'project_id'];
            $this->arrayMustHasKeys(head(head($resp)['tags']), $createrKeys);
        }

        if (isset(head($resp)['resources'])) {
            $createrKeys = ['id', 'creater_id', 'filename', 'project_id', 'mime', 'origin_name', 'ext_name'];
            $this->arrayMustHasKeys(head(head($resp)['resources']), $createrKeys);
        }
    }
    /**
     * 获取分享列表清单分页测试
     */
    public function testPaginateIndexTest()
    {
        $this->seedDB();
        $testProject = Project::firstOrFail();
        $req = ['per_page' => 10];
        $resp = $this->action('get', 'ProjectSharingController@index', [$testProject['id']], $req)->getData(true);
        $data = $resp['data'];

        $respKeys = ['total', 'per_page', 'current_page', 'last_page', 'from', 'to', 'data'];
        $this->arrayMustHasKeys($resp, $respKeys);

        $sharingKeys = ['id', 'name', 'content', 'project_id', 'created_at', 'updated_at',  'creater', 'tags', 'resources'];
        $this->arrayMustHasKeys(head($data), $sharingKeys);

        $createrKeys = ['id', 'username', 'email', 'description', 'head_image'];
        $this->arrayMustHasKeys(head($data)['creater'], $createrKeys);


        if (isset(head($data)['tags'])) {
            $createrKeys = ['id', 'name', 'project_id'];
            $this->arrayMustHasKeys(head(head($data)['tags']), $createrKeys);
        }


        if (isset(head($data)['resources'])) {
            $createrKeys = ['id', 'creater_id', 'filename', 'project_id', 'mime', 'origin_name', 'ext_name'];
            $this->arrayMustHasKeys(head(head($data)['resources']), $createrKeys);
        }

    }

    /**
     * 测试方法： store, 只附带必须的域
     */
    public function testStoreWithMustField()
    {
        list($testUser, $testProject) = $this->getTestUserAndProject();

        $postData = [
            'name'=>'testSharingName',
            'content'=>'testSharingContent',
        ];

        $add = $this->actionStoreAndReturnTestObj($testProject['id'], $postData);
        $keys = array_keys($postData);
        $this->arrayMustHasEqualKeyValues($add->toArray(), $postData, $keys);

        $postData['resource'] = [];
        $postData['tag'] = [];

        $add = $this->actionStoreAndReturnTestObj($testProject['id'], $postData);
        $this->arrayMustHasEqualKeyValues($add->toArray(), $postData, $keys);
    }

    /**
     * 测试方法： store, 附带资源
     */
    public function testStoreWithResource()
    {
        list($testUser, $testProject) = $this->getTestUserAndProject();

        $testFileInfo = $this->getOneTestFileInfo();
        $postData = [
            'name'=>'testSharingName',
            'content'=>'testSharingContent',
            'resource' => [ $testFileInfo ]
        ];

        //以数组的形式添加附加资源
        $add = $this->actionStoreAndReturnTestObj($testProject['id'], $postData);
        $addResource = $add->resource()->first();
        $keys = array_keys($testFileInfo);
        $this->arrayMustHasEqualKeyValues($testFileInfo, $addResource->toArray(), $keys);

        //以单个的形式添加附加资源
        $testFileInfo['filename'] = $testFileInfo['filename'] . 'One';
        $postData['name'] = 'OneMockSharingName';
        $postData['resource'] = $testFileInfo;
        $otherAdd = $this->actionStoreAndReturnTestObj($testProject['id'], $postData);
        $otherAddResource = $otherAdd->resource()->first();
        $this->arrayMustHasEqualKeyValues($testFileInfo, $otherAddResource->toArray(), $keys);

    }

    /**
     * 测试方法： store, 附带标签（以及资源）
     */
    public function testStoreWithTags()
    {
        list($testUser, $testProject) = $this->getTestUserAndProject();

        $postData = [
            'name'=>'sharingNameWithTag',
            'content'=>'sharingContentWithTag',
            'resource'=>$this->getOneTestFileInfo(),
            'tag'=>$this->buildRandomTagAndReturnId($testProject['id'])
        ];

        //以单个元素的形式添加附加标签
        $add = $this->actionStoreAndReturnTestObj($testProject['id'], $postData);
        $this->assertEquals($postData['tag'], $add->tag()->first()['id']);

        $postData['tag'] = [
            $this->buildRandomTagAndReturnId($testProject['id']),
            $this->buildRandomTagAndReturnId($testProject['id']),
        ];

        //以数组的形式添加附加标签
        $otherAdd = $this->actionStoreAndReturnTestObj($testProject['id'], $postData);
        $this->assertCount(count($postData['tag']), $otherAdd['tag']->toArray());

    }

    /**
     * 测试方法： destroy， 操作应该是成功的
     */
    public function testDestroyRight()
    {
        $testTarget = ProjectSharing::firstOrFail();
        $testTargetId = $testTarget['id'];
        $testProjectId = $testTarget['project']['id'];

        $resp = $this->ProjectSharingController->destroy($testProjectId, $testTargetId);
        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertNull( ProjectSharing::find($testTargetId));
    }

    /**
     * 测试方法：destroy, 操作应该是失败的.
     */
    public function testDestroyWrong()
    {
        $testProjectId = Project::firstOrFail()['id'];
        $this->setExpectedException('Illuminate\Database\Eloquent\ModelNotFoundException');
        $this->ProjectSharingController->destroy($testProjectId, ProjectSharing::count() + 10010);
    }

    /**
     * 测试方法： show
     */
    public function testShow()
    {
        $this->seedDB();
        $testTarget = ProjectSharing::firstOrFail();

        $resp = $this->ProjectSharingController->show($testTarget['project']['id'], $testTarget['id']);
        $this->assertEquals(200, $resp->getStatusCode());

        $respDataArray = $resp->getData(true);
        $keys = ['id', 'name', 'content', 'creater_id', 'creater', 'tag', 'resource', 'created_at'];

        $this->arrayMustHasKeys($respDataArray, $keys, true);
        $this->assertEquals($respDataArray['id'], $testTarget['id']);

    }

    /**
     * 构建随机的标签， 并返回新创建记录的id
     *
     * @param $projectId int 标签所属于的项目id
     * @return int
     */
    protected function buildRandomTagAndReturnId($projectId)
    {
        return
            Tag::insertGetId([
                'name'=>str_random(4),
                'project_id'=>$projectId,
                'created_at'=>date('Y-m-d h:m:s'),
                'updated_at'=>date('Y-m-d h:m:s')
            ]);
    }

    /**
     * 执行数据库填充
     */
    public function seedDB()
    {
        $this->seed('ResourceTableTestSeeder');
        $this->seed('SharingResourceTableTestSeeder');
        $this->seed('TagTableTestSeeder');
        $this->seed('SharingTagTableTestSeeder');
    }

    /**
     * 返回测试的用户实例和项目实例
     *
     * @return array
     */
    protected function getTestUserAndProject()
    {
        $testProject = $this->testUser->createProjects()->first();

        return [$this->testUser, $testProject];
    }

    /**
     * 执行 store 操作，并返回新创建的 ProjectSharing 实例.
     *
     * @param $projectId
     * @param $postData
     * @return \Illuminate\Support\Collection|static
     */
    protected function actionStoreAndReturnTestObj($projectId, $postData)
    {
        $resp = $this->action('post', 'ProjectSharingController@store', [$projectId], $postData);

        $this->assertResponseOk();

        return ProjectSharing::findOrFail( $resp->getData(true)['id'] );
    }

    /**
     * 得到一个测试文件信息
     * @return array
     */
    protected function getOneTestFileInfo()
    {
        return ['filename'=>'mockFilename', 'origin_name'=>'origin_name', 'ext_name'=>'jpg', 'mime'=>'image/jpeg'];
    }

    private $ProjectSharingController; //项目分享控制器实例
    private $testUser;  //引用模拟的用户的模型实例
}