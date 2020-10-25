<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-3-6
 * Time: 下午9:35
 */

class TaskControllerPaginationTest extends TestCase
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
     * 测试index方法，不分组但分页
     */
    public function testGetTasksWithoutGroup()
    {
        $this->seedDB();
        $targetProject = Project::first();

        $reqData = ['per_page' => 1];
        $resp = $this->action('get', 'TaskController@index', [ $targetProject['id'] ], $reqData);
        $this->assertResponseOk();

        $respDataArray = $resp->getData(true);
        $keys = ['id', 'name', 'status_id', 'priority_id', 'expected_at'];
        $this->arrayMustHasKeys(head($respDataArray['data']), $keys, true);
        $this->arrayMustHasKeys(head($respDataArray['data']), ['parent_id']);
    }

    /**
     * 测试index方法，返回数据是不分组的,但分页，且只获取某一状态任务
     */
    public function testIndexWithOutGroupButPaginate()
    {
        $this->seedDB();
        $targetProject = Project::first();

        $requestData = ['status'=>'doing', 'per_page'=>2, 'page'=>1, 'priority_id'=>1];
        $resp = $this->action('get', 'TaskController@index', [ $targetProject['id'] ], $requestData);
        $this->assertResponseOk();

        $respDataArray = $resp->getData(true);

        //基本信息必须具备这些字段
        $keys = ['id', 'parent_id', 'name', 'description', 'expected_at', 'finished_at'];
        $this->arrayMustHasKeys(head($respDataArray['data']), $keys);
    }

    /**
     * 测试index方法，分组、分页
     */
    public function testGetTasksWithGroup()
    {
        $this->seedDB();
        $targetProject = Project::first();

        $reqData = ['group' => true, 'per_page' => 1];
        $resp = $this->action('get', 'TaskController@index', [ $targetProject['id'] ], $reqData);
        $this->assertResponseOk();

        $respData = $resp->getData(true);
        $keys = ['undo', 'doing', 'finished'];
        $this->arrayMustHasKeys($respData, $keys);

        $keys = ['total', 'per_page', 'current_page', 'last_page', 'from', 'data'];
        $this->arrayMustHasKeys( head($respData), $keys);
        $this->assertEquals( $reqData['per_page'], count( head($reqData) ) );
    }

    /**
     *测试index方法，对应的是使用`offset`和`size`实现任意分页时的情况
     */
    public function testGetTasksWithOffsetAndSize()
    {
        $this->seedDB();
        $targetProject = Project::first();

        $requestData = ['group' => true, 'status'=>'doing', 'offset' => 1, 'size'=>1];
        $resp = $this->action('get', 'TaskController@index', [ $targetProject['id'] ], $requestData);
        $this->assertResponseOk();

        $respDataArray = $resp->getData(true);
        $this->assertTrue( isset($respDataArray[ $requestData['status'] ] ));

        //基本信息必须具备这些字段
        $keys = ['id', 'parent_id', 'name', 'description', 'expected_at', 'finished_at'];
        $this->arrayMustHasKeys(head($respDataArray[ $requestData['status'] ]), $keys);
    }

    private function seedDB()
    {
        $this->seed('UserTableTestSeeder');
        $this->seed('ProjectTableTestSeeder');
        $this->seed('ProjectTaskTableSeeder');
    }


    private $taskController;
}
