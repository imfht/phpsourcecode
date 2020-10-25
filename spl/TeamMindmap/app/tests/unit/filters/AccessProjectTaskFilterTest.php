<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-11-12
 * Time: 下午4:43
 */
class AccessProjectTaskFilterTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->seed('UserTableTestSeeder');
        $this->seed('ProjectTableTestSeeder');
        $this->seed('ProjectMemberTableTestSeeder');
        $this->seed('ProjectTaskTableSeeder');
        $this->seed('ProjectTaskMemberTableSeeder');


        $this->be(User::find(1));

        $this->accessProjectTaskFilter = $this->app->make('Libraries\Filter\AccessProjectTaskFilter');
        $this->routeMock = $this->mock('\Illuminate\Routing\Route');
        $this->requestMock = $this->mock('\Illuminate\Http\Request');

    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testIndex()
    {
        $this->setParentId(0);
        $this->setAction('ProjectTaskController@index');
        $this->setProjectId(1);
        $resp = $this->accessProjectTaskFilter->filter($this->routeMock, $this->requestMock);
        $this->assertEmpty($resp);

    }

    public function testStore()
    {
        $this->setParentId(1);
        $this->setAction('store');
        $this->setProjectId(1);

        $resp = $this->accessProjectTaskFilter->filter($this->routeMock, $this->requestMock);
        $this->assertEmpty($resp);
    }

    public function testShow()
    {
        $this->setParentId(0);
        $this->setAction('ProjectTaskController@show');
        $this->setProjectId(1);
        $resp = $this->accessProjectTaskFilter->filter($this->routeMock, $this->requestMock);
        $this->assertEmpty($resp);
    }

    public function testUpdate()
    {
        $this->setParentId(1);
        $this->setAction('update');
        $this->setProjectId(1);

        $resp = $this->accessProjectTaskFilter->filter($this->routeMock, $this->requestMock);
        $this->assertEmpty($resp);
    }

    public function destroyFilter()
    {
        $this->setParentId(1);
        $this->setAction('destroy');
        $this->setParentId(1);

        $resp = $this->accessProjectTaskFilter->filter($this->routeMock, $this->requestMock);
        $this->assertEmpty($resp);
    }


    private function setAction($action)
    {
        $this->routeMock
            ->shouldReceive('getActionName')
            ->once()
            ->andReturn('ProjectTaskController@' . $action);
    }

    private function setProjectId($projectId)
    {
        $this->routeMock
            ->shouldReceive('getParameter')
            ->once()
            ->andReturn($projectId);
    }

    private function setParentId($parentId)
    {
        $this->requestMock
            ->shouldReceive('get')
            ->once()
            ->andReturn($parentId);
    }

    private $accessProjectTaskFilter;

    private $routeMock;

    private $requestMock;

}