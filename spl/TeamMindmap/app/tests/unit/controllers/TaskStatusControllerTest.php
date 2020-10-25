<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-11-19
 * Time: 下午11:55
 */

class TaskStatusControllerTest extends TestCase
{
    /**
     * 初始化
     */
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->seed('ProjectTaskStatusTableSeeder');

        $this->controllerObj = $this->app->make('TaskStatusController');
    }

    /**
     * 测试方法: index
     */
    public function testIndex()
    {
        $resp = $this->controllerObj->index();
        $this->assertEquals(200, $resp->getStatusCode());

        $respDataArray = $resp->getData(true);
        $this->assertTrue( is_array($respDataArray) );
        $this->assertEquals(ProjectTaskStatus::count(), count($respDataArray));
        $this->checkKeys( head($respDataArray) );
    }

    /**
     * 测试方法： show
     */
    public function testShow()
    {
        $this->seed('ProjectTaskStatusTableSeeder');

        $resp = $this->controllerObj->show( ProjectTaskStatus::firstOrFail()['id'] );
        $this->assertEquals(200, $resp->getStatusCode());

        $respDataArray = $resp->getData(true);
        $this->assertTrue( is_array($respDataArray) );
        $this->checkKeys($respDataArray);

    }

    /**
     * 内部使用，检查数组键值对是否定义
     * @param $dataArray
     */
    protected function checkKeys($dataArray)
    {
        $keys = ['id', 'name', 'label'];
        $this->arrayMustHasKeys($dataArray, $keys, true);
    }

    private $controllerObj;
}