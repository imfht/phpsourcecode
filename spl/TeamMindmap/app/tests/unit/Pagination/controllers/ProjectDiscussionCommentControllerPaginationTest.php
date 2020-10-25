<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-3-6
 * Time: 下午9:47
 */

/** 项目讨论区评论控制器测试
 * Class ProjectDiscussionCommentControllerPaginationTest
 */
class ProjectDiscussionCommentControllerPaginationTest extends TestCase
{
    public function  setUp()
    {
        parent::setUp();
        Artisan::call('migrate');

        $this->controllerObj = $this->app->make('ProjectDiscussionCommentController');
    }

    public function testGetComments()
    {
        list($testUser, $testProject, $testDiscussion) = $this->getTestData();
        $this->seed('ProjectDiscussionCommentsTableTestSeeder');

        $resp = $this->action('get', 'ProjectDiscussionCommentController@getIndex', [ $testProject['id'], $testDiscussion['id'] ], ['per_page' => 1]);
        $this->assertResponseOk();

        $respData = $resp->getData(true);
        $keys = [ 'total', 'per_page', 'current_page', 'last_page', 'from', 'to', 'data' ];
        $this->arrayMustHasKeys( $respData, $keys );
    }


    protected function seedDB()
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
        $testDiscussion = ProjectDiscussion::where('project_id', $testProject['id'])->firstOrFail();

        return [$testUser, $testProject, $testDiscussion];
    }

    protected $controllerObj;// 项目讨论区评论控制器实例
}