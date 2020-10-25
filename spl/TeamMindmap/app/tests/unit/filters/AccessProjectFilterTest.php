<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-10-25
 * Time: 下午12:44
 */

/**
 * Class AccessProjectFilterTest
 *
 * 此类用于对过滤器类AccessProjectFilter类进行单元测试
 */
class AccessProjectFilterTest extends TestCase
{

    /**
     * 进行一些初始化操作： 模拟用户登陆、执行数据库迁移、创建Mock对象（Route、Request）
     */
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');
        Artisan::call('db:seed');
        $this->seed('UserTableTestSeeder');

        $this->user = User::find(1);
        $this->be( $this->user );

        $this->accessProjectFilter = $this->app->make('Libraries\Filter\AccessProjectFilter');
        $this->routeMock = $this->mock('\Illuminate\Routing\Route');
        $this->requestMock = $this->mock('\Illuminate\Http\Request');
    }

    /**
     * 进行一些回复操作.
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * 测试过滤器对show方法的过滤，此方法检查能通过过滤的校验。
     */
    public function testShowRight()
    {
        $this->seed('ProjectTableTestSeeder');
        $this->seed('ProjectMemberTableTestSeeder');

        $projectId = $this->user->createProjects->first()->id;
        $this->checkRight('show', $projectId);

        $projectId = $this->user->joinProjects->first()->id;
        $this->checkRight('show', $projectId);
    }

    /**
     * 测试过滤器对show方法的过滤，此方法检查不能通过过滤的校验。
     */
    public function testShowWrong()
    {
        $this->checkWrong('show', 1);
    }

    /**
     * 测试过滤器对destroy方法的过滤，此方法检查能通过过滤的校验。
     */
    public function testDestroyRight()
    {
        $this->seed('ProjectTableTestSeeder');
        $projectId = $this->user->createProjects->first()->id;
        $this->checkRight('destroy', $projectId);
    }

    /**
     * 测试过滤器对destroy方法的过滤，此方法检查不能通过过滤的校验。
     */
    public function testDestroyWrong()
    {
        $this->seed('ProjectTableTestSeeder');
        $this->seed('ProjectMemberTableTestSeeder');

        $this->checkWrong('destroy', 100000);

        $projectId = $this->user->joinProjects->first()->id;
        $this->checkWrong('destroy', $projectId);
    }

    /**
     * 测试过滤器对update方法的过滤
     */
    public function testUpdate()
    {
        $this->seed('ProjectTableTestSeeder');
        $this->seed('ProjectMemberTableTestSeeder');

        $projectId = $this->user->createProjects->first()->id;
        $this->checkRight('update', $projectId);

        $projectId = $this->user->joinProjects->first()->id;
        $this->checkWrong('update', $projectId);
        $this->checkWrong('update', 10000);
    }

    /**
     * 设置在RouteMock对象，即设置下一次请求中要检查的ProjectController方法.
     ** @param $action 方法的名称
     */
    protected function setAction($action)
    {
        $this->routeMock
            ->shouldReceive('getActionName')
            ->once()
            ->andReturn('ProjectController@'. $action);
    }

    /**
     * 设置在RouteMock对象，即设置下一次请求中要检查的Project ID.
     *
     * @param $projectId
     */
    protected function setProjectId($projectId)
    {
        $this->routeMock
            ->shouldReceive('getParameter')
            ->once()
            ->andReturn($projectId);
    }

    /**
     * 调用AccessProjectFilter类中的Filter方法.
     *
     * @param $action
     * @param $projectId
     * @return mixed
     */
    protected function callFilter($action, $projectId)
    {
        $this->setAction($action);
        $this->setProjectId($projectId);

        $resp = $this->accessProjectFilter->filter($this->routeMock, $this->requestMock);

        return $resp;
    }

    /**
     * 检查是否通过了过滤.
     *
     * @param $action
     * @param $projectId
     */
    protected function checkRight($action, $projectId)
    {
        $resp = $this->callFilter($action, $projectId);
        $this->assertEmpty($resp);
    }

    /**
     * 检查是否没有铜鼓过滤.
     *
     * @param $action
     * @param $projectId
     */
    protected function checkWrong($action, $projectId)
    {
        $resp = $this->callFilter($action, $projectId);
        $this->assertEquals(403, $resp->getStatusCode());

    }

    private $accessProjectFilter;		//应用AccessProjectFilter的实例

    private $routeMock;		//引用Route的Mock对象的实例

    private $requestMock; //应用Request的Mock对象的实例

    private $user;	//引用模拟登陆的用户

}