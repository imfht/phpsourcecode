<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-3-6
 * Time: 下午9:43
 */

/** 项目讨论控制器分页业务测试
 * Class ProjectDiscussionControllerPaginationTest
 */
class ProjectDiscussionControllerPaginationTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->controllerObj = $this->app->make('ProjectDiscussionController');
    }

    /**
     * 测试获取讨论列表：列表的筛选条件为：all，返回相应的分页数据
     */
    public function testGetDiscussionsWithAll()
    {
        list($testUser, $testProject) = $this->getTestData();
        $resq = ['per_page' => 1];
        $respData = $this->commonAction($resq, $testProject, 1);
    }

    /**
     * 测试获取讨论列表：列表的筛选条件为：open & create，返回相应的分页数据
     */
    public function testGetDiscussionsWOpenAndCreate()
    {
        list($testUser, $testProject) = $this->getTestData();
        $resq = ['open' => 1, 'user' => 2, 'per_page' => 1];
        $respData = $this->commonAction($resq, $testProject, 1);
    }


    /**
     * @param $resq
     * @param $expectCount
     * @return mixed
     */
    protected function commonAction($resq, $testProject, $expectCount)
    {
        $this->seed('ProjectDiscussionFollowerTableTestSeeder');

        $resp = $this->action('get', 'ProjectDiscussionController@index', [ $testProject['id'] ], $resq);
        $this->assertResponseOk();

        $respData = $resp->getData(true);
        $keys = [ 'total', 'per_page', 'current_page', 'last_page', 'from', 'to', 'data' ];
        $this->arrayMustHasKeys( $respData, $keys );
        $this->assertEquals( $expectCount, count($respData['data']));
        return $respData;
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