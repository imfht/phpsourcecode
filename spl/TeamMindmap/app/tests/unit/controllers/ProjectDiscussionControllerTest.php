<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-12-25
 * Time: 上午12:56
 */

class ProjectDiscussionControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->controllerObj = $this->app->make('ProjectDiscussionController');
    }

    /**
     * 测试: index
     */
    public function testIndexWithoutCond()
    {
        $this->seedDB();
        $testProject = Project::firstOrFail();
        $this->be( User::firstOrFail() );

        $resp = $this->action('get', 'ProjectDiscussionController@index', [ $testProject['id'] ], []);
        $this->assertEquals(200, $resp->getStatusCode());

        $respDataArray = $resp->getData(true);
        $this->assertTrue( is_array($respDataArray) );

        $keys = ['id', 'title', 'creater', 'created_at', 'updated_at', 'open', 'content'];
        $this->arrayMustHasKeys(head($respDataArray), $keys, true);
    }

    public function testIndexWithCond_open_and_follow()
    {
        list($testUser, $testProject) = $this->getTestData();
        $this->seed('ProjectDiscussionFollowerTableTestSeeder');
        $resp = $this->action('get', 'ProjectDiscussionController@index', [ $testProject['id'] ], ['open' => 1, 'user' => 1]);
        $this->assertEquals(200, $resp->getStatusCode());

        $respData = $resp->getData(true);
        $countFollow = ProjectDiscussion::leftJoin('projectDiscussion_follower',
            'projectDiscussions.id', '=',
            'projectDiscussion_follower.projectDiscussion_id')
            ->where('projectDiscussions.project_id', $testProject['id'])
            ->where('projectDiscussion_follower.follower_id', $testUser['id'])
            ->count();

        $this->assertEquals($countFollow, count($respData));
    }



    public function testShowRight()
    {

        $this->seedDB();
        $this->seed('ProjectDiscussionCommentsTableTestSeeder');
        $this->seed('ProjectDiscussionFollowerTableTestSeeder');
        $testProject = Project::firstOrFail();
        $testDiscussion = head( ProjectDiscussion::where('project_id', $testProject['id'])->get()->toArray() );

        $resp = $this->controllerObj->show($testProject['id'], $testDiscussion['id']);
        $this->assertEquals(200, $resp->getStatusCode());

        $respData = $resp->getData(true);
        $keys = ['baseInfo','creater', 'followers'];
        $this->arrayMustHasKeys($respData, $keys, true);

        $keys = ['id', 'title', 'content'];
        $this->arrayMustHasKeys($respData['baseInfo'], $keys);
    }

    /**
     * 测试方法: store， 应该是操作成功的
     */
    public function testStoreRight()
    {
        list($testUser, $testProject) = $this->getTestData();

        $postData = [
            'title'=>'just take a test',
            'content'=>'just a test content',
            'followers'=>[2, 3]
        ];

        $resp = $this->action('post', 'ProjectDiscussionController@store', [ $testProject['id'] ], $postData);
        $this->assertResponseOk();

        $respDataArray = $resp->getData(true);
        $newAdd = ProjectDiscussion::findOrFail($respDataArray['id']);
        $this->arrayMustHasEqualKeyValues($postData, $newAdd->toArray(), ['title', 'content']);
        $this->arrayMustHasEqualKeyValues($newAdd['creater']->toArray(), $testUser->toArray(), ['id']);

        foreach($postData['followers'] as $followerId){
            $this->assertNotEmpty( DB::table('projectDiscussion_follower')->where('follower_id', $followerId)->get() );
        }

    }

    /**
     * 测试方法： store，操作应该是失败的
     */
    public function testStoreWrong()
    {
        list($testUser, $testProject) = $this->getTestData();

        $postData = [
            'title'=>'just take a test',
            'followers'=>[2, 3]
        ];

        $this->action('post', 'ProjectDiscussionController@store', [ $testProject['id'] ], $postData);
        $this->assertResponseStatus(403);

        unset($postData['title']);
        $postData['content'] = 'has content';
        $this->action('post', 'ProjectDiscussionController@store', [ $testProject['id'] ], $postData);
        $this->assertResponseStatus(403);

        $postData['title'] = 'has title';
        $postData['followers'] = 3;
        $this->action('post', 'ProjectDiscussionController@store', [ $testProject['id'] ], $postData);
        $this->assertResponseStatus(403);
    }

    /**
     * 测试方法：update
     */
    public function testUpdate()
    {
        list($testUser, $testProject) = $this->getTestData();

        $testDiscussion = ProjectDiscussion::where('project_id', $testProject['id'])
            ->firstOrFail();

        $putData = [
          'open'=> ! $testDiscussion['open']
        ];

        $this->action('put', 'ProjectDiscussionController@update', [ $testProject['id'], $testDiscussion['id'] ], $putData);
        $this->assertResponseOk();
        $this->assertNotEmpty(
            ProjectDiscussion::where('id', $testDiscussion['id'])
                ->where('open', $putData['open'])
                ->get()
        );

    }

    /**
     * 执行数据库填充
     */
    public function seedDB()
    {
        $this->seed('UserTableTestSeeder');
        $this->seed('ProjectTableTestSeeder');
        $this->seed('ProjectDiscussionsTableTestSeeder');
    }


    /**
     * 得到一些测使用的数据，此处返回 测试用户、测试项目
     * @return array
     */
    protected function getTestData()
    {
        $this->seedDB();
        $testUser = User::firstOrFail();
        $this->be($testUser);
        $testProject = $testUser->createProjects()->first();

        return [$testUser, $testProject];
    }


    private $controllerObj;
}