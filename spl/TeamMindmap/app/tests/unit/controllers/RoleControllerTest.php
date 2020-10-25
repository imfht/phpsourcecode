<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-11-12
 * Time: 上午12:17
 */

/**
 * Class RoleControllerTest
 *
 * 用于测试控制器RoleController
 */
class RoleControllerTest extends TestCase
{
    /**
     * 进行一些数据库填从及类实例化
     */
    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->roleControllerObj = $this->app->make('RoleController');
    }

    /**
     * 测试RoleController的Index方法
     */
    public function testIndex()
    {
        $respDataArray = $this->roleControllerObj->index()->getData(true);

        $this->assertCount(ProjectRole::count(), $respDataArray);
        $this->arrayMustHasKeys(head($respDataArray));
    }

    /**
     * 测试RoleController的show方法
     */
    public function testShow()
    {
        $respDataArray = $this->roleControllerObj->show(1)->getData(true);
        $this->assertTrue( is_array($respDataArray) && count($respDataArray) > 0 );
        $this->arrayMustHasKeys($respDataArray);
    }

    /**
     * 对父类方法的封装，添加默认的键值.
     *
     * @param array $dataArray
     * @param array $keys
     */
    protected function arrayMustHasKeys(array $dataArray, array $keys = null)
    {
        if( ! is_array($keys) ){
            $keys = ['name', 'id', 'label'];
        }

        parent::arrayMustHasKeys($dataArray, $keys);

    }

    private $roleControllerObj; //引用RoleController的实例
}