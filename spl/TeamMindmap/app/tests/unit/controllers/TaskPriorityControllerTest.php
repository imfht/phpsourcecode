<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-12-23
 * Time: 下午5:18
 */

class TaskPriorityControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');

        $this->controllerObj = $this->app->make('TaskPriorityController');
    }

    /**
     * 测试方法： index
     */
    public function testIndex()
    {
        $this->seedDB();
        $resp = $this->controllerObj->index();
        $this->assertEquals(200, $resp->getStatusCode());

        $respDataArray = $resp->getData(true);
        $this->assertTrue( is_array($respDataArray) );

        $keys = ['id', 'name', 'label'];
        $this->arrayMustHasKeys( head($respDataArray), $keys, true);
    }

    /**
     * 测试方法： show
     */
    public function testShow()
    {
        $this->seedDB();
        $testOne = ProjectTaskPriority::firstOrFail();

        $resp = $this->controllerObj->show($testOne['id']);
        $this->assertEquals(200, $resp->getStatusCode());

        $respDataArray = $resp->getData(true);
        $keys = ['id', 'name', 'label'];
        $this->assertTrue( is_array($respDataArray) );
        $this->arrayMustHasEqualKeyValues($respDataArray, $testOne->toArray(), $keys);
    }

    protected function seedDB()
    {
        $this->seed('ProjectTaskPrioritiesTableSeeder');
    }

    private $controllerObj;
}