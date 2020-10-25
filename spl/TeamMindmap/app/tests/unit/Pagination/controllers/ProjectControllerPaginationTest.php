<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-3-6
 * Time: 下午9:23
 */

/** 此类用于测试控制器: ProjectController 分页业务
 * Class ProjectControllerPaginationTest
 */
class ProjectControllerPaginationTest extends TestCase{

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


    /**
     * 测试分页时获取所有类别
     */
    public function testGetAllProjects()
    {
        $resq = ['per_page' => 10, 'option' => 'all'];
        $keys = ['id', 'name', 'cover', 'introduction', 'creater_id', 'created_at', 'updated_at'];
        $respDataArray = $this->commonAction($resq, $keys, 2);
    }

    /**
     * 测试分页时获取用户关联的项目中的 create　类别
     */
    public function testGetCreateProjects()
    {
        $resq = ['per_page' => 1, 'option' => 'create'];
        $keys = ['id', 'name', 'cover', 'introduction', 'creater_id', 'created_at', 'updated_at'];
        $respDataArray = $this->commonAction($resq, $keys, 1);

        $this->arrayMustHasEqualKeyValues(['creater_id' => $this->testUser['id']], $respDataArray['data'][0], ['creater_id']);
    }

    /**
     * 测试分页时获取用户关联的项目中的 join　类别
     */
    public function testGetJoinProjects()
    {

        $resq = ['per_page' => 10, 'option' => 'join'];
        $keys = ['id', 'name', 'cover', 'introduction', 'creater_id', 'created_at', 'updated_at'];
        $respDataArray = $this->commonAction($resq, $keys, 1);
    }

    /**
     * @param $resq
     * @param $keys
     * @param $expectCount
     * @return mixed
     */
    protected function commonAction($resq, $keys, $expectCount)
    {
        $this->seedDB();

        $resp = $this->action('get', 'ProjectController@index', null ,$resq);
        $this->assertEquals(200, $resp->getStatusCode());
        $respDataArray = $resp->getData(true);

        $this->assertCount($expectCount, $respDataArray['data']);
        $this->arrayMustHasKeys(head($respDataArray['data']), $keys);
        return $respDataArray;
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