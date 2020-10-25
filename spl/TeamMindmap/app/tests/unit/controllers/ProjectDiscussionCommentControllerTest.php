<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 14-12-25
 * Time: 下午6:20
 */

/**
 * 项目讨论区评论控制器测试
 * Class ProjectDiscussionCommentControllerTest
 */
class ProjectDiscussionCommentControllerTest extends TestCase
{
    public function  setUp()
    {
        parent::setUp();
        Artisan::call('migrate');

        $this->controllerObj = $this->app->make('ProjectDiscussionCommentController');
    }

    public function testGetIndexWithoutPaginate()
    {
        list($testUser, $testProject, $testDiscussion) = $this->getTestData();
        $this->seed('ProjectDiscussionCommentsTableTestSeeder');

        $resp = $this->action('get', 'ProjectDiscussionCommentController@getIndex', [ $testProject['id'], $testDiscussion['id'] ], []);
        $this->assertResponseOk();

        $respData = $resp->getData(true);
        $keys = [ 'id', 'content', 'creater', 'created_at', 'updated_at' ];
        $this->arrayMustHasKeys( head($respData), $keys );
    }


    public function testPostIndex()
    {
        list($testUser, $testProject, $testDiscussion) = $this->getTestData();

        $postData = [
            'content'=>'has a content'
        ];

        $resp = $this->action('post', 'ProjectDiscussionCommentController@postIndex', [$testProject['id'], $testDiscussion['id']], $postData);
        $this->assertResponseOk();

        $respDataArray = $resp->getData(true);
        $newAdd = ProjectDiscussionComment::findOrFail($respDataArray['id']);
        $this->assertEquals($newAdd['content'], $postData['content']);
        $this->assertEquals($newAdd['creater_id'], $testUser['id']);
        $this->assertEquals($newAdd['projectDiscussion_id'], $testDiscussion['id']);
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